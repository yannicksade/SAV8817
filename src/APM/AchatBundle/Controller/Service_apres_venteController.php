<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Service_apres_vente;
use APM\AchatBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Transaction_produit;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use SebastianBergmann\CodeCoverage\RuntimeException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Service_apres_vente controller.
 * @RouteResource("sav", pluralize=false)
 */
class Service_apres_venteController extends Controller
{
    private $offre_filter;
    private $boutique_filter;
    private $desc_filter;
    private $etat_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $affiliation_filter;
    private $code_filter;
    private $commentaire_filter;
    private $client_filter;

    /**
     * Liste tous les SAV enregistrés entant que client, les SAV receptionnés en tant que boutique ou les SAV d'une offre
     * @ParamConverter("offre", options={"mapping": {"offre_id":"id"}})
     * liste les SAV du clients
     * @param Request $request
     * @param Boutique $boutique
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\Response| JsonResponse Liste tous les SAV d'un client
     *
     * @Get("/cget/services", name="s")
     * @Get("/cget/services/boutique/{id}", name="s_boutique")
     * @Get("/cget/services/offre/{offre_id}", name="s_offre")
     */
    public function getAction(Request $request, Boutique $boutique = null, Offre $offre = null)
    {
        $this->listAndShowSecurity();
        $services = new ArrayCollection();
        if (null !== $boutique) {
            $this->listAndShowSecurity($boutique, null);
            $offres = $boutique->getOffres();
            /** @var Offre $offre */
            foreach ($offres as $offre) {
                $service_apres_ventes = $offre->getServiceApresVentes();
                foreach ($service_apres_ventes as $service) {
                    $services->add($service);
                }
            }

        } elseif (null !== $offre) {
            $this->listAndShowSecurity(null, $offre);
            $services = $offre->getServiceApresVentes();
        } else {// recupérer les sav de toutes les boutiques de l'utilisateur
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            if ($request->query->has("q")
                && $request->query->get("q") === "boutiques"
            ) {
                //----------------------------------Boutiques -----------------------------------
                $boutiques = $user->getBoutiquesGerant();
                /** @var Boutique $boutique */
                foreach ($boutiques as $boutique) {
                    $offres = $boutique->getOffres();
                    /** @var Offre $offre */
                    foreach ($offres as $offre) {
                        $service_apres_ventes = $offre->getServiceApresVentes();
                        foreach ($service_apres_ventes as $service) {
                            $services->add($service);
                        }
                    }
                }
                $boutiques = $user->getBoutiquesProprietaire();
                /** @var Boutique $boutique */
                foreach ($boutiques as $boutique) {
                    $offres = $boutique->getOffres();
                    /** @var Offre $offre */
                    foreach ($offres as $offre) {
                        $service_apres_ventes = $offre->getServiceApresVentes();
                        foreach ($service_apres_ventes as $service) {
                            $services->add($service);
                        }
                    }
                }
            } else {
                $service_apres_ventes = $user->getServicesApresVentes();
                foreach ($service_apres_ventes as $service) {
                    $services->add($service);
                }
            }
        }
        $json = array();
        $session = $this->get('session');
        try { //----- Security control  -----
            //filter parameters
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->offre_filter = $request->query->has('offre_filter') ? $request->query->get('offre_filter') : "";
            $this->boutique_filter = $request->query->has('boutique_filter') ? $request->query->get('boutique_filter') : "";
            $this->desc_filter = $request->query->has('desc_filter') ? $request->query->get('desc_filter') : "";
            $this->dateFrom_filter = $request->query->has('date_from_filter') ? $request->query->get('date_from_filter') : "";
            $this->dateTo_filter = $request->query->has('date_to_filter') ? $request->query->get('date_to_filter') : "";
            $this->etat_filter = $request->query->has('etat_filter') ? $request->query->get('etat_filter') : "";
            $this->affiliation_filter = $request->query->has('affiliation_filter') ? $request->query->get('affiliation_filter') : "";
            $this->commentaire_filter = $request->query->has('commentaire_filter') ? $request->query->get('commentaire_filter') : "";
            $this->client_filter = $request->query->has('client_filter') ? $request->query->get('client_filter') : "";
            $iDisplayLength = intval($request->query->get('length'));
            $iDisplayStart = intval($request->query->get('start'));
            $iTotalRecords = count($services); // counting
            $services = $this->handleResults($services, $iTotalRecords, $iDisplayStart, $iDisplayLength); // filtering
            $iFilteredRecords = count($services);
            //------------------------------------
            $data = $this->get('apm_core.data_serialized')->getFormalData($services, array("owner_list"));
            $json['totalRecords'] = $iTotalRecords;
            $json['filteredRecords'] = $iFilteredRecords;
            $json['items'] = $data;
            return new JsonResponse($json, 200);
        } catch (AccessDeniedException $ads) {
            $session->getFlashBag()->add('danger', "<strong>Opération interdite!</strong><br/>Pour jouir de ce service, veuillez consulter nos administrateurs.");
            return new JsonResponse('Access denied', 200);
        }
    }

    /**
     * @param Boutique |null $boutique
     * @param Offre |null $offre
     */
    private
    function listAndShowSecurity($boutique = null, $offre = null)
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) throw $this->createAccessDeniedException();
        }
        if ($offre) {
            $vendeur = $offre->getVendeur();
            $boutique = $offre->getBoutique();
            $gerant = null;
            $proprietaire = null;
            if ($boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
            }
            if ($user !== $vendeur && $user !== $gerant && $user !== $proprietaire) throw $this->createAccessDeniedException();
        }

        //-----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $services
     * @return array
     */
    private function handleResults($services, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        if ($services == null) return array();

        if ($this->affiliation_filter === 'P') {
            $services = $services->filter(function ($e) {//filtering per owner
                /** @var Service_apres_vente $e */
                return $e->getOffre()->getBoutique()->getProprietaire() === $this->getUser();
            });
        } elseif ($this->affiliation_filter === 'G') { //filtering per shop keeper
            $services = $services->filter(function ($e) {
                /** @var Service_apres_vente $e */
                return $e->getOffre()->getBoutique()->getGerant() === $this->getUser();
            });
        }
        if ($this->etat_filter != null) {
            $services = $services->filter(function ($e) {//filtrage select
                /** @var Service_apres_vente $e */
                return $e->getEtat() === $this->etat_filter;
            });
        }
        if ($this->dateFrom_filter != null) {
            $services = $services->filter(function ($e) {//start date
                /** @var Service_apres_vente $e */
                $dt1 = (new \DateTime($e->getDateDue()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $services = $services->filter(function ($e) {//end date
                /** @var Service_apres_vente $e */
                $dt = (new \DateTime($e->getDateDue()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->client_filter != null) {
            $services = $services->filter(function ($e) {//filter with the begining of the entering word
                /** @var Service_apres_vente $e */
                $str1 = $e->getClient()->getCode();
                $str2 = $this->client_filter;
                return strcasecmp($str1, $str2) === 0 ? true : false;
            });
        }
        if ($this->boutique_filter != null) {
            $services = $services->filter(function ($e) {//filter with the begining of the entering word
                /** @var Service_apres_vente $e */
                $str1 = $e->getOffre()->getBoutique()->getDesignation();
                $str2 = $this->boutique_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->offre_filter != null) {
            $services = $services->filter(function ($e) {//filter with the begining of the entering word
                /** @var Service_apres_vente $e */
                $str1 = $e->getOffre()->getDesignation();
                $str2 = $this->offre_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->desc_filter != null) {
            $services = $services->filter(function ($e) {//search for occurences in the text
                /** @var Service_apres_vente $e */
                $subject = $e->getDescriptionPanne();
                $pattern = $this->desc_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->commentaire_filter != null) {
            $services = $services->filter(function ($e) {//search for occurences in the text
                /** @var Service_apres_vente $e */
                $subject = $e->getCommentaire();
                $pattern = $this->commentaire_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $services = ($services !== null) ? $services->toArray() : [];
        //------ filtering and paging -----
        usort(//assortment: descending of date -- du plus recent au plus ancient
            $services, function ($e1, $e2) {
            /**
             * @var Service_apres_vente $e1
             * @var Service_apres_vente $e2
             */
            $dt1 = $e1->getDateDue()->getTimestamp();
            $dt2 = $e2->getDateDue()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $services = array_slice($services, $iDisplayStart, $iDisplayLength, true); //slicing, preserve the keys' order

        return $services;
    }

    /**
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|JsonResponse
     *
     * @Post("/new/sav")
     * @Post("/new/sav/offre/{id}", name="_offre")
     */
    public
    function newAction(Request $request, Offre $offre = null)
    {
        $this->createSecurity($offre);
        /** @var Session $session */
        $session = $request->getSession();
        /** @var Service_apres_vente $service_apres_vente */
        $service_apres_vente = TradeFactory::getTradeProvider("service_apres_vente");
        $form = $this->createForm('APM\AchatBundle\Form\Service_apres_venteType', $service_apres_vente);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (null !== $offre) $service_apres_vente->setOffre($offre);
                $this->createSecurity($service_apres_vente->getOffre());
                $service_apres_vente->setClient($this->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($service_apres_vente);
                $em->flush();
                $session->getFlashBag()->add('success', "<strong> Création de l'Offre. réf:" . $offre->getCode() . "</strong><br> Opération effectuée avec succès!");
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json["item"] = array();
                    return $this->json(json_encode($json), 200);
                }
                return $this->redirectToRoute('apm_achat_service_apres_vente_show', array('id' => $service_apres_vente->getId()));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'opération. </strong><br>Vous devez avoir achetés l'offre sur WE-TRADE au préalable!!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        $session->set('previous_location', $request->getUri());
        return $this->render('APMAchatBundle:service_apres_vente:new.html.twig', array(
            'service_apres_vente' => $service_apres_vente,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Offre|null $offre , il s'agit du produit, figurant dans les transaction du client. (acheté par celui-ci)
     */
    private
    function createSecurity($offre = null)
    {
        //-----------------security: L'utilisateur doit etre le client qui a acheté l'offre -----------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $clientDeOffre = false;

        if ($offre) {
            $produit_transactions = $offre->getProduitTransactions();
            if (null !== $produit_transactions) {
                /** @var Transaction_produit $produit_transaction */
                foreach ($produit_transactions as $produit_transaction) {
                    $produit = $produit_transaction->getProduit();
                    $client = $produit_transaction->getTransaction()->getBeneficiaire();
                    if ($produit === $offre && $client === $this->getUser()) {
                        $clientDeOffre = true;
                        break;
                    }
                }
            }
            if (!$clientDeOffre) throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Service_apres_vente entity.
     * @param Service_apres_vente $service_apres_vente
     * @return JsonResponse Afficher ls détails d'une SAV
     *
     * @Get("/show/sav/{id}")
     */
    public function showAction(Service_apres_vente $service_apres_vente)
    {
        $this->listAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($service_apres_vente, ["owner_sav_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * Displays a form to edit an existing Service_apres_vente entity.
     * @param Request $request
     * @param Service_apres_vente $service_apres_vente
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response |JsonResponse
     *
     * @Put("/edit/sav/{id}")
     */
    public function editAction(Request $request, Service_apres_vente $service_apres_vente)
    {
        $this->editAndDeleteSecurity($service_apres_vente);
        $deleteForm = $this->createDeleteForm($service_apres_vente);
        $editForm = $this->createForm('APM\AchatBundle\Form\Service_apres_venteType', $service_apres_vente);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()
            || $request->isXmlHttpRequest() && $request->isMethod('POST')
        ) {
            try {
                $em = $this->getDoctrine()->getManager();
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json["item"] = array();
                    $property = $request->query->get('name');
                    $value = $request->query->get('value');
                    switch ($property) {
                        case 'etat':
                            $service_apres_vente->setEtat($value);
                            break;
                        case 'description':
                            $service_apres_vente->setDescriptionPanne($value);
                            break;
                        case 'commentaire' :
                            $service_apres_vente->setCommentaire($value);
                            break;
                        default:
                            $session->getFlashBag()->add('info', "<strong> Aucune mise à jour effectuée </strong>");
                            return $this->json(json_encode(["item" => null]), 205);
                    }
                    $em->flush();
                    $session->getFlashBag()->add('success', "Mise à jour propriété : <strong>" . $property . "</strong> réf. offre :" . $service_apres_vente->getCode() . "<br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                $em->flush();
                $session->getFlashBag()->add('success', "Mise à jour propriété : <strong>" . $property . "</strong> réf. offre :" . $service_apres_vente->getCode() . "<br> Opération effectuée avec succès!");
                return $this->redirectToRoute('apm_achat_service_apres_vente_show', array('id' => $service_apres_vente->getId()));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "Echec de la Modification <br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "Action interdite!<br>Vous n'êtes pas autorisés à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        return $this->render('APMAchatBundle:service_apres_vente:edit.html.twig', array(
            'service_apres_vente' => $service_apres_vente,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Service_apres_vente $service_apres_vente
     */
    private
    function editAndDeleteSecurity($service_apres_vente)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $client = null;
        if (null !== $service_apres_vente) {
            $client = $service_apres_vente->getClient();
            $user = $this->getUser();
            if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($client !== $user)) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Creates a form to delete a Service_apres_vente entity.
     *
     * @param Service_apres_vente $service_apres_vente The Service_apres_vente entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private
    function createDeleteForm(Service_apres_vente $service_apres_vente)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_achat_service_apres_vente_delete', array('id' => $service_apres_vente->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
//------------------------ End INDEX ACTION --------------------------------------------

    /**
     * Deletes a Service_apres_vente entity.
     * @param Request $request
     * @param Service_apres_vente $service_apres_vente
     * @return \Symfony\Component\HttpFoundation\RedirectResponse| JsonResponse
     *
     * @Delete("/delete/sav/{id}")
     */
    public function deleteAction(Request $request, Service_apres_vente $service_apres_vente)
    {
        $this->editAndDeleteSecurity($service_apres_vente);
        /** @var Session $session */
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            /** @var Service_apres_vente $service_apres_vente */
            $items = $request->query->get('items');
            $elements = json_decode($items);
            $json = array();
            $json['item'] = array();
            $j = 0;
            $count = count($elements);
            for ($i = 0; $i < $count; $i++) {
                $service_apres_vente = null;
                $id = $elements[$i];
                $service_apres_vente = $em->getRepository('APMAchatBundle:Service_apres_vente')->find($id);
                if (null !== $service_apres_vente) {
                    $this->editAndDeleteSecurity($service_apres_vente);
                    $em->remove($service_apres_vente);
                    $em->flush();
                    $json['item'] = $id;
                    $j++;
                }
            }
            $session->getFlashBag()->add('danger', "<strong>" . $j . "</strong> Element(s) supprimé(s)<br> Opération effectuée avec succès!");
            return $this->json(json_encode($json), 200);
        }
        $form = $this->createDeleteForm($service_apres_vente);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($service_apres_vente);
            $em->flush();
        }
        return $this->redirectToRoute('apm_achat_service_apres_vente_index');
    }


    /**
     * @param Service_apres_vente $service_apres_vente
     */
    private
    function deleteSecurity($service_apres_vente = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
            throw $this->createAccessDeniedException();
        }
        if (null !== $service_apres_vente) {
            if ($service_apres_vente->getClient() !== $this->getUser()) throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Offre $offre non null, l'accès est autorisée uniquement au gérant et au proprietaire
     * @param $client
     * @param $user
     */
    private
    function editSecurity($offre, $client, $user)
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $vendeur = $offre->getVendeur();
        $boutique = $offre->getBoutique();
        $gerant = null;
        $proprietaire = null;
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
        }
        if ($user !== $client && $user !== $vendeur && $user !== $gerant && $user !== $proprietaire) throw $this->createAccessDeniedException();

        //-----------------------------------------------------------------------------------------
    }
}
