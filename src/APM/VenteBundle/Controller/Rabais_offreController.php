<?php

namespace APM\VenteBundle\Controller;

use APM\UserBundle\Entity\Groupe_relationnel;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Rabais_offre;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Rabais_offre controller.
 * @RouteResource("discount", pluralize=false)
 */
class Rabais_offreController extends Controller
{
    private $beneficiaire_filter;
    private $code_filter;
    private $nombreDefois_filter;
    private $quantite_filter;
    private $groupe_filter;
    private $vendeur_filter;
    private $offre_filter;
    private $prixUpdateMax_filter;
    private $prixUpdateMin_filter;
    private $dateLimiteTo_filter;
    private $dateLimiteFrom_filter;


    /**
     * Le vendeur crée des rabais pour un utilisateur donné
     * Liste les rabais créé par le vendeur
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Get("/cget/rabaisoffres/utilisateur", name="s")
     * @Get("/cget/rabaisoffres/offre/{id}", name="s_offre")
     */
    public function getAction(Request $request, Offre $offre = null)
    {
        $this->listAndShowSecurity($offre);
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
            $q = $request->get('q');
            $this->beneficiaire_filter = $request->query->has('beneficiaire_filter') ? $request->query->get('beneficiaire_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->dateLimiteFrom_filter = $request->query->has('dateLimiteFrom_filter') ? $request->query->get('dateLimiteFrom_filter') : "";
            $this->dateLimiteTo_filter = $request->query->has('dateLimiteTo_filter') ? $request->query->get('dateLimiteTo_filter') : "";
            $this->nombreDefois_filter = $request->query->has('nombreDefois_filter') ? $request->query->get('nombreDefois_filter') : "";
            $this->prixUpdateMin_filter = $request->query->has('prixUpdateMin_filter') ? $request->query->get('prixUpdateMin_filter') : "";
            $this->prixUpdateMax_filter = $request->query->has('prixUpdateMax_filter') ? $request->query->get('prixUpdateMax_filter') : "";
            $this->quantite_filter = $request->query->has('quantite_filter') ? $request->query->get('quantite_filter') : "";
            $this->vendeur_filter = $request->query->has('vendeur_filter') ? $request->query->get('vendeur_filter') : "";
            $this->offre_filter = $request->query->has('offre_filter') ? $request->query->get('offre_filter') : "";
            $this->groupe_filter = $request->query->has('groupe_filter') ? $request->query->get('groupe_filter') : "";
            $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            $json = array();
            $rabais_offres = null;
        $json['items'] = array();
            if ($q === "fromProduct" || $q === "all") {
                if (null !== $offre) $rabais_offres = $offre->getRabais();
                if (null !== $rabais_offres) {
                    $iTotalRecords = count($rabais_offres);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $rabais_offres = $this->handleResults($rabais_offres, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    $iFilteredRecords = count($rabais_offres);
                    $data = $this->get('apm_core.data_serialized')->getFormalData($rabais_offres, array("owner_list"));
                    $json['totalRecordsFromProduct'] = $iTotalRecords;
                    $json['filteredRecordsFromProduct'] = $iFilteredRecords;
                    $json['items'] = $data;
                }
            }

            if ($q === "sent" || $q === "all") {
                $rabais_recus = $user->getRabaisRecus();
                if (null !== $rabais_recus) {
                    $iTotalRecords = count($rabais_recus);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $rabais_recus = $this->handleResults($rabais_recus, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    $iFilteredRecords = count($rabais_offres);
                    $data = $this->get('apm_core.data_serialized')->getFormalData($rabais_recus, array("owner_list"));
                    $json['totalRecordsSent'] = $iTotalRecords;
                    $json['filteredRecordsSent'] = $iFilteredRecords;
                    $json['items'] = $data;
                }
            }

            if ($q === "received" || $q === "all") {
                $rabais_accordes = $user->getRabaisAccordes();
                if (null !== $rabais_accordes) {
                    $iTotalRecords = count($rabais_accordes);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $rabais_accordes = $this->handleResults($rabais_accordes, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    $iFilteredRecords = count($rabais_accordes);
                    $data = $this->get('apm_core.data_serialized')->getFormalData($rabais_accordes, array("others_list"));
                    $json['totalRecordsReceived'] = $iTotalRecords;
                    $json['filteredRecordsReceived'] = $iFilteredRecords;
                    $json['items'] = $data;
                }
            }
        return new JsonResponse($json, 200);
    }

    /**
     * @param Rabais_offre|null $rabais
     * @param Offre $offre
     */
    private function listAndShowSecurity($offre = null, $rabais = null)
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }

        if ($offre) { /// N'autorise que le vendeur, le gerant ou le proprietaire ou le bénéficiare à pouvoir afficher des rabais sur l'offre
            $user = $this->getUser();
            $boutique = $offre->getBoutique();
            $gerant = null;
            $proprietaire = null;
            $beneficiaire = null;
            if ($boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
            }
            $vendeur = $offre->getVendeur();
            if ($rabais) {//beneficiare
                $beneficiaire = $rabais->getBeneficiaireRabais();
            }
            if ($user !== $gerant && $user !== $proprietaire && $user !== $vendeur && $user !== $beneficiaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $rabais
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($rabais, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($rabais === null) return array();

        if ($this->code_filter != null) {
            $rabais = $rabais->filter(function ($e) {//filtrage select
                /** @var Rabais_offre $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->quantite_filter != null) {
            $rabais = $rabais->filter(function ($e) {//filtrage select
                /** @var Rabais_offre $e */
                return $e->getQuantiteMin() <= $this->quantite_filter;
            });
        }
        if ($this->groupe_filter != null) {
            $rabais = $rabais->filter(function ($e) {//filtrage select
                /** @var Rabais_offre $e */
                return $e->getGroupe()->getCode() === intval($this->groupe_filter);
            });
        }
        if ($this->prixUpdateMin_filter != null) {
            $rabais = $rabais->filter(function ($e) {//filtrage select
                /** @var Rabais_offre $e */
                return $e->getPrixUpdate() >= intval($this->prixUpdateMin_filter);
            });
        }
        if ($this->prixUpdateMax_filter != null) {
            $rabais = $rabais->filter(function ($e) {//filtrage select
                /** @var Rabais_offre $e */
                return $e->getPrixUpdate() <= intval($this->prixUpdateMax_filter);
            });
        }
        if ($this->dateLimiteFrom_filter != null) {
            $rabais = $rabais->filter(function ($e) {//start date
                /** @var Rabais_offre $e */
                $dt1 = (new \DateTime($e->getDateLimite()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateLimiteFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateLimiteTo_filter != null) {
            $rabais = $rabais->filter(function ($e) {//end date
                /** @var Rabais_offre $e */
                $dt = (new \DateTime($e->getDateLimite()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateLimiteTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->offre_filter != null) {
            $rabais = $rabais->filter(function ($e) {//filter with the begining of the entering word
                /** @var Rabais_offre $e */
                $str1 = $e->getOffre()->getDesignation();
                $str2 = $this->offre_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }

        if ($this->beneficiaire_filter != null) {
            $rabais = $rabais->filter(function ($e) {//search for occurences in the text
                /** @var Rabais_offre $e */
                $subject = $e->getBeneficiaireRabais()->getUsername();
                $pattern = $this->beneficiaire_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->vendeur_filter != null) {
            $rabais = $rabais->filter(function ($e) {//search for occurences in the text
                /** @var Rabais_offre $e */
                $subject = $e->getVendeur()->getUsername();
                $pattern = $this->vendeur_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $rabais = ($rabais !== null) ? $rabais->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $rabais, function ($e1, $e2) {
            /**
             * @var Rabais_offre $e1
             * @var Rabais_offre $e2
             */
            $dt1 = $e1->getDateLimite()->getTimestamp();
            $dt2 = $e2->getDateLimite()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $rabais = array_slice($rabais, $iDisplayStart, $iDisplayLength, true);

        return $rabais;
    }

    /**
     * Creates a new Rabais_offre entity.
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Post("/new/rabaioffre/{id}")
     */
    public function newAction(Request $request, Offre $offre)
    {
        $this->createSecurity($offre);
        /** @var Session $session */
        $session = $request->getSession();
        /** @var Rabais_offre $rabais_offre */
        $rabais_offre = TradeFactory::getTradeProvider('rabais');
        $form = $this->createForm('APM\VenteBundle\Form\Rabais_offreType', $rabais_offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->createSecurity($offre, $rabais);
                $rabais_offre->setVendeur($this->getUser());
                $rabais_offre->setOffre($offre);
                $em = $this->getDoctrine()->getManager();
                $em->persist($rabais_offre);
                $em->flush();
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json["item"] = array(//prevenir le client
                        "action" => 0,
                    );
                    $session->getFlashBag()->add('success', "<strong> rabais d'offre créée. réf:" . $rabais_offre->getCode() . "</strong><br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                return $this->redirectToRoute('apm_vente_rabais_offre_show', array('id' => $rabais_offre->getId()));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (RuntimeException $rte) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'opération.</strong><br>L'enregistrement a échoué. bien vouloir réessayer plutard, svp!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        $session->set('previous_location', $request->getUri());
        return $this->render('APMVenteBundle:rabais_offre:new.html.twig', array(
            'offre' => $offre,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Offre $offre
     * @param Rabais_offre|null $rabais
     */
    private function createSecurity($offre = null, $rabais = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        if ($offre) { /// N'autorise que le vendeur, le gerant ou le proprietaire à pouvoir  faire des rabais
            $user = $this->getUser();
            $boutique = $offre->getBoutique();
            $gerant = null;
            $proprietaire = null;
            $beneficiaire = null;
            if ($boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
            }
            $vendeur = $offre->getVendeur();
            if ($rabais) $beneficiaire = $rabais->getBeneficiaireRabais();
            //le beneficiaire du rabais ne peut être celui qui le cree et le createur ne devrait être que le vendeur ayant droit,
            if ($user !== $gerant && $user !== $proprietaire && $user !== $vendeur || $beneficiaire === $user) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Rabais_offre entity.
     * @param Rabais_offre $rabais_offre
     * @return JsonResponse
     *
     * @Get("/show/rabaioffre/{id}")
     */
    public function showAction(Rabais_offre $rabais_offre)
    {
        $this->listAndShowSecurity($rabais_offre->getOffre(), $rabais_offre);
        $data = $this->get('apm_core.data_serialized')->getFormalData($rabais_offre, ["owner_rabais_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * Displays a form to edit an existing Rabais_offre entity.
     * @param Request $request
     * @param Rabais_offre $rabais_offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Put("/edit/rabaioffre/{id}")
     */
    public function editAction(Request $request, Rabais_offre $rabais_offre)
    {
        $this->editAndDeleteSecurity($rabais_offre);
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $json = array();
            $json['item'] = array();
            /** @var Session $session */
            $session = $request->getSession();
            $em = $this->getDoctrine()->getManager();
            $property = $request->request->get('name');
            $value = $request->request->get('value');
            switch ($property) {
                case 'dateLimite':
                    $rabais_offre->setDateLimite($value);
                    break;
                case 'nombreDeFois':
                    $rabais_offre->setNombreDefois($value);
                    break;
                case 'prixUpdate':
                    $rabais_offre->setPrixUpdate($value);
                    break;
                case 'quantiteMin' :
                    $rabais_offre->setQuantiteMin($value);
                    break;
                case 'offre' :
                    /** @var Offre $offre */
                    $offre = $em->getRepository('APMVenteBundle:Offre')->find($value);
                    $rabais_offre->setOffre($offre);
                    break;
                case 'groupe' :
                    $groupe = $em->getRepository('APMUserBundle:Groupe_relationnel')->find($value);
                    $rabais_offre->setGroupe($groupe);
                    break;
                case 'beneficiaire':
                    /** @var Utilisateur_avm $beneficiaire */
                    $beneficiaire = $em->getRepository('APMUserBundle:Utilisateur_avm')->find($value);
                    $rabais_offre->setBeneficiaireRabais($beneficiaire);
                    break;
                default:
                    $session->getFlashBag()->add('info', "<strong> Aucune mis à jour effectuée</strong>");
                    return $this->json(json_encode(["item" => null]), 205);
            }
            $em->flush();
            $session->getFlashBag()->add('success', "Mis à jour propriété : <strong>" . $property . "</strong> réf. rabais d'offre:" . $rabais_offre->getCode() . "<br> Opération effectuée avec succès!");
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($rabais_offre);
        $editForm = $this->createForm('APM\VenteBundle\Form\Rabais_offreType', $rabais_offre);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($rabais_offre);
            $em = $this->getDoctrine()->getManager();
            $em->persist($rabais_offre);
            $em->flush();

            return $this->redirectToRoute('apm_vente_rabais_offre_show', array('id' => $rabais_offre->getId()));
        }

        return $this->render('APMVenteBundle:rabais_offre:edit.html.twig', array(
            'rabais_offre' => $rabais_offre,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Rabais_offre $rabais
     */
    private function editAndDeleteSecurity($rabais)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        /// N'autorise que le vendeur, le gerant ou le proprietaire à pouvoir modifier ou supprimer des rabais sur l'offre
        // à condition qu'ils ne soyent pas ledit bénéficiaire
        $boutique = $rabais->getOffre()->getBoutique();
        $user = $this->getUser();
        $gerant = null;
        $proprietaire = null;
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
        }
        $vendeur = $rabais->getOffre()->getVendeur();
        if ($user !== $gerant && $user !== $proprietaire && $user !== $vendeur || $user === $rabais->getBeneficiaireRabais()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Creates a form to delete a Rabais_offre entity.
     *
     * @param Rabais_offre $rabais_offre The Rabais_offre entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Rabais_offre $rabais_offre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_rabais_offre_delete', array('id' => $rabais_offre->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Deletes a Rabais_offre entity.
     * @param Request $request
     * @param Rabais_offre $rabais_offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     *
     * @Delete("/delete/rabaioffre/{id}")
     */
    public function deleteAction(Request $request, Rabais_offre $rabais_offre)
    {
        $this->editAndDeleteSecurity($rabais_offre);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $em->remove($rabais_offre);
            $em->flush();
            $json = array();
            return $this->json($json, 200);
        }
        $form = $this->createDeleteForm($rabais_offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($rabais_offre);
            $em->remove($rabais_offre);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_rabais_offre_index', ['id' => $rabais_offre->getOffre()->getId()]);
    }
}
