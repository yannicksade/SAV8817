<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Service_apres_vente;
use APM\AchatBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Transaction_produit;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Driver\SQLAnywhere\SQLAnywhereException;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use SebastianBergmann\CodeCoverage\RuntimeException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Service_apres_vente controller.
 *
 */
class Service_apres_venteController extends Controller
{
    /**
     * Liste tous les SAV enregistrés entant que client et les SAV receptionné en tant que boutique ou les SAV d'une offre
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function indexAction(Request $request)
    {
        //---------------------------post------------------------
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {

            $em = $this->getDoctrine()->getManager();
            $session = $this->get('session');
            /** @var Service_apres_vente $service_apres_vente */
            $data = $request->request->get('service_apres_vente');
            $service_apres_vente = null;
            $id = intval($data['id']);
            if (is_numeric($id)) $service_apres_vente = $em->getRepository('APMAchatBundle:Service_apres_vente')->find($id);
            $json['item'] = array();
            if (null !== $service_apres_vente) { //Update- Mise à jour
                try {
                    //autorisation d'accès
                    //***---double vérification à l'aide du code ---
                    if ($data['code'] !== $service_apres_vente->getCode()) {
                        $session->getFlashBag()->add('danger', "Vous n'êtes pas autorisé à effectuer cette opération: <br>Vous devez au préalable achêté le produit sur la plateforme!");
                        return $this->json(json_encode(["item" => null]));
                    }
                    //***-----------------------------------------
                    $client = $service_apres_vente->getClient();
                    $user = $this->getUser();
                    //------------------ security: allow only the client, the manger and the owner of the product-----------------------
                    $this->editSecurity($service_apres_vente->getOffre(), $client, $user);
                    $description = "undefined";
                    $commentaire = "undefined";
                    $etat = "undefined";
                    if ($client === $user) {//auteur -> faire les mises à jour d'attributs ici
                        $description = $data['descriptionPanne'];
                        if ($service_apres_vente->getDescriptionPanne() !== $description) $service_apres_vente->setDescriptionPanne($description);
                    } else {// destinataire -> faire les mises à jour d'attributs ici
                        $commentaire = $data['commentaire'];
                        if ($service_apres_vente->getCommentaire() !== $commentaire) $service_apres_vente->setCommentaire($commentaire);
                        $etat = $data['etat'];
                        if ($service_apres_vente->getEtat() !== $etat) $service_apres_vente->setEtat($etat);
                    }
                    //----------------------------------------------------------------------------------------
                    //-------- prepareration de la reponse du client ----

                    $json["item"] = array(//preparation de la réponse du client
                        "descriptionPanne" => $description,
                        "etat" => $etat,
                        "commentaire" => $commentaire,
                        "isNew" => false,
                        "id" => $service_apres_vente->getId(),
                    );
                    // préparation de la notification du client
                    if ($description !== "undefined" || $commentaire !== "undefined" || $etat !== "undefined") {
                        $em->flush();
                        $session->getFlashBag()->add('success', "<strong> Mise à jour, référence:" . $service_apres_vente->getCode() . "</strong><br>Modification effectuée avec succès!");
                        return $this->json(json_encode($json));
                    } else {
                        $session->getFlashBag()->add('info', "<strong> Mise à jour!</strong><br>Aucune modification effectuée.");
                        return $this->json(json_encode(["item" => null]));
                    }
                } catch (ConstraintViolationException $cve) {
                    $session->getFlashBag()->add('danger', "Echec de l'enregistrement: <br>L'enregistrement a échoué dû à une contrainte de données!");
                    return $this->json(json_encode(["item" => null]));
                } catch (RuntimeException $rte) {
                    $session->getFlashBag()->add('danger', "Echec de l'opération: <br>L'enregistrement a échoué. bien vouloir réessayer plutard, svp!");
                    return $this->json(json_encode(["item" => null]));
                } catch (AccessDeniedException $ads) {
                    $session->getFlashBag()->add('danger', "Vous n'êtes pas autorisé à effectuer cette opération: <br>Vous devez au préalable achêté le produit sur la plateforme!");
                    return $this->json(json_encode(["item" => null]));
                }
            } else {// create a new element
                try { // valider  le formulaire ici
                    //---create security 1------//
                    $this->createSecurity();
                    /** @var Service_apres_vente $service_apres_vente */
                    $service_apres_vente = TradeFactory::getTradeProvider("service_apres_vente");
                    if (null !== $service_apres_vente) {
                        /** @var Offre $offre */
                        $offreID = intval($data['offre']);
                        $offre = null;
                        if (is_numeric($offreID)) $offre = $em->getRepository('APMVenteBundle:Offre')->find($offreID);
                        if (null !== $offre) {
                            //---create security 2------//
                            $this->createSecurity($offre);
                            $service_apres_vente->setOffre($offre);
                            $service_apres_vente->setClient($this->getUser());
                            $service_apres_vente->setDescriptionPanne($data['descriptionPanne']); //if (isset($data['descriptionPanne']))
                            $em->persist($service_apres_vente);
                            $em->flush();
                            $json["item"]["isNew"] = true;
                            $session->getFlashBag()->add('success', "<strong> Soumission de votre requête, référence:" . $service_apres_vente->getCode() . "</strong><br>Opération effectuée avec succès!");
                            return $this->json(json_encode($json));
                        } else {
                            $session->getFlashBag()->add('danger', "<strong>Produit non sélectionné.</strong> <br>Vous devez indiquer le produit!");
                            return $this->json(json_encode(["item" => null]));
                        }
                    }
                    $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>Un problème est survenu et l'enregistrement a échoué! veuillez réessayer dans un instant!");
                    return $this->json(json_encode(["item" => null]));
                } catch (ConstraintViolationException $cve) {
                    $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                    return $this->json(json_encode(["item" => null]));
                } catch (RuntimeException $rte) {
                    $session->getFlashBag()->add('danger', "<strong>Echec de l'opération.</strong><br>L'enregistrement a échoué. bien vouloir réessayer plutard, svp!");
                    return $this->json(json_encode(["item" => null]));
                } catch (AccessDeniedException $ads) {
                    $session->getFlashBag()->add('danger', "<strong>Echec de l'opération. </strong><br>L'enregistrement a échoué dû à une contrainte de données!!");
                    return $this->json(json_encode(["item" => null]));
                }
            }
        }
        //------------------ Form---------------
        $form = $this->createForm('APM\AchatBundle\Form\Service_apres_venteType');
        $form2 = $this->createForm('APM\AchatBundle\Form\Service_apres_venteType');
        return $this->render('APMAchatBundle:service_apres_vente:index.html.twig', array(
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));

    }

    private $offre_filter;
    private $boutique_filter;
    private $desc_filter;
    private $etat_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $affiliation_filter;

    //private $code_filter;

    public function loadTableAction(Request $request)
    {
        //------------------
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $records = array();
            $records['data'] = array();
            $session = $this->get('session');
            try { //----- Security control  -----
                $this->listAndShowSecurity();
                //filter parameters
                //$this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
                $this->offre_filter = $request->request->has('offre_filter') ? $request->request->get('offre_filter') : "";
                $this->boutique_filter = $request->request->has('boutique_filter') ? $request->request->get('boutique_filter') : "";
                $this->desc_filter = $request->request->has('desc_filter') ? $request->request->get('desc_filter') : "";
                $this->dateFrom_filter = $request->request->has('date_from_filter') ? $request->request->get('date_from_filter') : "";
                $this->dateTo_filter = $request->request->has('date_to_filter') ? $request->request->get('date_to_filter') : "";
                $this->etat_filter = $request->request->has('etat_filter') ? $request->request->get('etat_filter') : "";
                $this->affiliation_filter = $request->request->has('affiliation_filter') ? $request->request->get('affiliation_filter') : "";
                $iDisplayLength = intval($request->request->get('length'));
                $iDisplayStart = intval($request->request->get('start'));
                $sEcho = intval($request->request->get('draw'));

                $pathInfo = $request->getPathInfo();

                $status_list = array(
                    array("danger" => "En panne"), //0
                    array("success" => "Problème résolu"),//1
                    array("info" => "En cours de diagnostic"),//2
                    array("info" => "En cours de dépannage"),//3
                    array("danger" => "Déclaré hors service"),//4
                    array("info" => "En observation"),//5
                    array("warning" => "Frais exigible"),//6
                    array("danger" => "Demande rejeté"),//7
                    array("info" => "Problème soumis"),//8
                );

                $service_apres_ventes = null;
                //-----Source -------
                /** @var Utilisateur_avm $user */
                $user = $this->getUser();
                $services = null;
                if ($pathInfo === '/apm/achat/service_apres_vente/ajax-table_1') {
                    $services = $user->getServicesApresVentes();
                } elseif ($pathInfo === '/apm/achat/service_apres_vente/ajax-table_2') {
                    //----------------------------------Boutiques -----------------------------------
                    $boutiques = $user->getBoutiquesGerant();
                    /** @var Boutique $boutique */
                    foreach ($boutiques as $boutique) {
                        $offres = $boutique->getOffres();
                        /** @var Offre $offre */
                        foreach ($offres as $offre) {
                            $services = $offre->getServiceApresVentes();
                        }
                    }
                    $boutiques = $user->getBoutiquesProprietaire();
                    /** @var Boutique $boutique */
                    foreach ($boutiques as $boutique) {
                        $offres = $boutique->getOffres();
                        /** @var Offre $offre */
                        foreach ($offres as $offre) {
                            $services2 = $offre->getServiceApresVentes();
                            foreach ($services2 as $s2) {
                                if (null !== $services) $services->add($s2);
                            }
                        }
                    }

                    //---end---
                }
                //page paremeters
                $iTotalRecords = count($services); // counting
                $services = $this->elementsFilter($services); // filtering
                $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
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
                $services = array_slice($services, $iDisplayStart, $iDisplayLength, true); //slicing, preserve the keys' order
                //------------------------------------
                $id = 0; // identity of rows in the table
                /** @var Service_apres_vente $service_apres_vente */
                foreach ($services as $service_apres_vente) {
                    $id += 1;
                    $etat = $service_apres_vente->getEtat();
                    $offre = $service_apres_vente->getOffre();
                    $boutique = $offre->getBoutique();
                    $panne = $service_apres_vente->getDescriptionPanne();
                    $records['data'][] = array(
                        '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id_' . $service_apres_vente->getId() . '" type="checkbox" class="checkboxes"/><span></span></label>',
                        '<span><i class="id hidden">' . $service_apres_vente->getId() . '</i><i class="hidden code">' . $service_apres_vente->getCode() . '</i><i class="hidden client">' . $service_apres_vente->getClient() . '</i><i class="hidden clientID">' . $service_apres_vente->getClient()->getId() . '</i><i class="hidden comment">' . $service_apres_vente->getCommentaire() . '</i>' . $id . '</span>',
                        '<span><i class="hidden offreID">' . $offre->getId() . '</i><i class="offre hidden">' . $offre->getDesignation() . '</i><a href="#">' . $offre->getDesignation() . '</a></span>',
                        '<span><i class="boutique hidden">' . $boutique->getDesignation() . '</i>' . $boutique->getDesignation() . '</span>',
                        '<span class="date">' . $service_apres_vente->getDateDue()->format("d/m/Y - H:i") . '</span>',
                        '<span><i class="hidden desc">' . $panne . '</i>' . $panne . '</span>',
                        '<span class="etat label label-sm label-' . (key($status_list[$etat])) . '"><input type="hidden" value="' . $etat . '"/>' . (current($status_list[$etat])) . '</span>',
                    );
                }
                $records['draw'] = $sEcho;
                $records["recordsTotal"] = $iTotalRecords;
                $records["recordsFiltered"] = $iTotalRecords;
                return $this->json($records);
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Opération interdite!</strong><br/>Pour jouir de ce service, veuillez consulter nos administrateurs.");
                return $this->json($records);
            } catch (RuntimeException $rte) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'opération: </strong><br>Une erreur systeme s'est produite. bien vouloir réessayer plutard, svp!");
                return $this->json(json_encode(["item" => null]));
            }
        }

        return new JsonResponse();
    }

//------------------------ End INDEX ACTION --------------------------------------------

    /**
     * @param Collection $services
     * @return array
     */
    private function elementsFilter($services)
    {
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
        return ($services != null) ? $services->toArray() : [];
    }


    public function deleteAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {

            $session = $this->get('session');
            try {
                $this->deleteSecurity();
                /** @var Service_apres_vente $service_apres_vente */
                $items = $request->request->get('items');
                $elements = json_decode($items);
                $em = $this->getDoctrine()->getManager();
                $json = null;
                $count = count($elements);
                for ($i = 0; $i < $count; $i++) {
                    $service_apres_vente = null;
                    $id = intval($elements[$i]);
                    if (is_numeric($id)) $service_apres_vente = $em->getRepository('APMAchatBundle:Service_apres_vente')->find($id);
                    if ($service_apres_vente !== null) {
                        //---- Secutity 2 : allow only the autor to delete -------
                        $this->deleteSecurity($service_apres_vente);
                        $em->remove($service_apres_vente);
                        $json[] = $id;
                        $session->getFlashBag()->add('danger', "<strong> Element de référence:" . $service_apres_vente->getCode() . "</strong><br> Supprimé avec succès!");
                    }
                }
                $em->flush();
                $json = json_encode(['ids' => $json]);
                return $this->json($json);
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Opération interdite!</strong><br/>Pour jouir de ce service, veuillez consulter nos administrateurs.");
                return $this->json($records);
            } catch (RuntimeException $rte) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'opération: </strong><br>Une erreur systeme s'est produite. bien vouloir réessayer plutard, svp!");
                return $this->json(json_encode(["item" => null]));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de la suppression d'un ou plusieurs elements. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        return new JsonResponse();
    }


    /**
     * @param Offre|null $offre , il s'agit du produit, figurant dans les transaction du client. (acheté par celui-ci)
     */
    private function createSecurity($offre = null)
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
            /** @var Transaction_produit $produit_transaction */
            foreach ($produit_transactions as $produit_transaction) {
                $produit = $produit_transaction->getProduit();
                $client = $produit_transaction->getTransaction()->getBeneficiaire();
                if ($produit === $offre && $client === $this->getUser()) {
                    $clientDeOffre = true;
                    break;
                }
            }
            if (!$clientDeOffre) throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    private function listAndShowSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Service_apres_vente $service_apres_vente
     */
    private function deleteSecurity($service_apres_vente = null)
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
    private function editSecurity($offre, $client, $user)
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
