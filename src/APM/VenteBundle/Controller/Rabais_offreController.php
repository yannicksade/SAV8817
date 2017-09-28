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

/**
 * Rabais_offre controller.
 *
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
     */
    public function indexAction(Request $request, Offre $offre = null)
    {
        $this->listAndShowSecurity($offre);
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $q = $request->get('q');
            $this->beneficiaire_filter = $request->request->has('beneficiaire_filter') ? $request->request->get('beneficiaire_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->dateLimiteFrom_filter = $request->request->has('dateLimiteFrom_filter') ? $request->request->get('dateLimiteFrom_filter') : "";
            $this->dateLimiteTo_filter = $request->request->has('dateLimiteTo_filter') ? $request->request->get('dateLimiteTo_filter') : "";
            $this->nombreDefois_filter = $request->request->has('nombreDefois_filter') ? $request->request->get('nombreDefois_filter') : "";
            $this->prixUpdateMin_filter = $request->request->has('prixUpdateMin_filter') ? $request->request->get('prixUpdateMin_filter') : "";
            $this->prixUpdateMax_filter = $request->request->has('prixUpdateMax_filter') ? $request->request->get('prixUpdateMax_filter') : "";
            $this->quantite_filter = $request->request->has('quantite_filter') ? $request->request->get('quantite_filter') : "";
            $this->vendeur_filter = $request->request->has('vendeur_filter') ? $request->request->get('vendeur_filter') : "";
            $this->offre_filter = $request->request->has('offre_filter') ? $request->request->get('offre_filter') : "";
            $this->groupe_filter = $request->request->has('groupe_filter') ? $request->request->get('groupe_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $json = array();
            $rabais_offres = null;
            if ($q === "fromProduct" || $q === "all") {
                if (null !== $offre) $rabais_offres = $offre->getRabais();
                if (null !== $rabais_offres) {
                    $iTotalRecords = count($rabais_offres);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $rabais_offres = $this->handleResults($rabais_offres, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    //filtre
                    /** @var Rabais_offre $rabai */
                    foreach ($rabais_offres as $rabai) {
                        array_push($json, array(
                            'value' => $rabai->getId(),
                            'text' => $rabai->getCode(),
                        ));
                    }
                }
            }

            if ($q === "sent" || $q === "all") {
                $rabais_recus = $user->getRabaisRecus();
                if (null !== $rabais_recus) {
                    $iTotalRecords = count($rabais_recus);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $rabais_recus = $this->handleResults($rabais_recus, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    //filtre
                    foreach ($rabais_recus as $rabai) {
                        array_push($json, array(
                            'value' => $rabai->getId(),
                            'text' => $rabai->getCode(),
                        ));
                    }
                }
            }

            if ($q === "received" || $q === "all") {
                $rabais_accordes = $user->getRabaisAccordes();
                if (null !== $rabais_accordes) {
                    $iTotalRecords = count($rabais_accordes);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $rabais_accordes = $this->handleResults($rabais_accordes, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    foreach ($rabais_accordes as $rabai) {
                        array_push($json, array(
                            'value' => $rabai->getId(),
                            'text' => $rabai->getCode(),
                        ));
                    }
                }
            }
            return $this->json($json, 200);
        }
        $rabais_accordes = $user->getRabaisAccordes();
        $rabais_recus = $user->getRabaisRecus();
        if (null !== $offre) $rabais_offres = $offre->getRabais();
        return $this->render('APMVenteBundle:rabais_offre:index.html.twig', array(
            'rabais_offres' => $rabais_offres,
            'rabais_recus' => $rabais_recus,
            'rabais_accordes' => $rabais_accordes,
            'offre' => $offre,
        ));
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
     * Creates a new Rabais_offre entity.
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Rabais_offre $rabais_offre)
    {
        $this->listAndShowSecurity($rabais_offre->getOffre(), $rabais_offre);
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $rabais_offre->getId(),
                'dateLimite' => $rabais_offre->getDateLimite()->format('d-m-Y H:i'),
                'nombreDeFois' => $rabais_offre->getNombreDefois(),
                'prixUpdate' => $rabais_offre->getPrixUpdate(),
                'beneficiaire' => $rabais_offre->getBeneficiaireRabais()->getUsername(),
                'vendeur' => $rabais_offre->getVendeur()->getUsername(),
                'quantiteMin' => $rabais_offre->getQuantiteMin(),
                'groupe' => $rabais_offre->getGroupe()->getDesignation(),
                'offre' => $rabais_offre->getOffre()->getDesignation(),
                'date' => $rabais_offre->getDate()->format('d-m-Y H:i'),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($rabais_offre);
        return $this->render('APMVenteBundle:rabais_offre:show.html.twig', array(
            'rabais_offre' => $rabais_offre,
            'delete_form' => $deleteForm->createView(),
        ));
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
     * Displays a form to edit an existing Rabais_offre entity.
     * @param Request $request
     * @param Rabais_offre $rabais_offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
     * Deletes a Rabais_offre entity.
     * @param Request $request
     * @param Rabais_offre $rabais_offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
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

    public function deleteFromListAction(Rabais_offre $rabais_offre)
    {
        $this->editAndDeleteSecurity($rabais_offre);

        $em = $this->getDoctrine()->getManager();
        $em->remove($rabais_offre);
        $em->flush();

        return $this->redirectToRoute('apm_vente_rabais_offre_index', ['id' => $rabais_offre->getOffre()->getId()]);
    }
}
