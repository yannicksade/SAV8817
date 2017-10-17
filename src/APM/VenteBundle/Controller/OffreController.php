<?php

namespace APM\VenteBundle\Controller;

use APM\AchatBundle\Entity\Groupe_offre;
use APM\UserBundle\Entity\Admin;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\Collection;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Categorie;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use SebastianBergmann\CodeCoverage\RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Offre controller.
 *
 */
class OffreController extends Controller
{
    /********************************************AJAX REQUEST********************************************/
    private
        $designation_filter;
    private
        $boutique_filter;
    private
        $code_filter;
    private
        $etat_filter;
    private
        $dateFrom_filter;
    private
        $dateTo_filter;
    private
        $status_list = array(
        array("success" => "Disponible en stock"), //0
        array("danger" => "Non disponible en stock"),//1
        array("info" => "Vente sur commande"),//2
        array("danger" => "Vente suspendue"),//3
        array("danger" => "Vente annulée"),//4
        array("warning" => "Stock limité"),//5
        array("warning" => "Article en panne"),//6
        array("info" => "Disponible uniquement en région"),//7
        array("danger" => "Vente interdite"),//8
    );

    /**
     * @ParamConverter("categorie", options={"mapping": {"categorie_id":"id"}})
     * @ParamConverter("user", options={"mapping": {"user_id":"id"}})
     * @ParamConverter("groupe_offre", options={"mapping": {"groupe_id":"id"}})
     * Liste les offres de la boutique ou du vendeur
     * @param Request $request
     * @param Boutique $boutique
     * @param Categorie $categorie
     * @param Utilisateur_avm $user
     * @param Groupe_offre|null $groupe_offre
     * @return JsonResponse|Response
     */
    public function indexAction(Request $request, Boutique $boutique = null, Categorie $categorie = null, Utilisateur_avm $user = null, Groupe_offre $groupe_offre = null)
    {
        try {
            /** @var Session $session */
            $session = $request->getSession();
            $vendeur = null;
            if (null !== $boutique) {
                $this->listAndShowSecurity($boutique);
                if ($categorie) {
                    $offres = $categorie->getOffres();
                } else {
                    $offres = $boutique->getOffres();
                }
            } elseif (null !== $groupe_offre) {
                $offres = $groupe_offre->getOffres();
            } else {
                if (null === $user) {
                    $this->listAndShowSecurity();
                    $user = $this->getUser();
                } else {
                    $this->adminSecurity();
                }
                /** @var Collection $offres */
                $offres = $user->getOffres();
                $vendeur = $user;
            }
            $form = $this->createForm('APM\VenteBundle\Form\OffreType');
            $form->handleRequest($request);
            /** @var Session $session */
            $session = $request->getSession();
            if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
                $json = array();
                $json['items'] = array();
                //filter parameters
                $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
                $this->designation_filter = $request->request->has('desc_filter') ? $request->request->get('desc_filter') : "";
                $this->boutique_filter = $request->request->has('boutique_filter') ? $request->request->get('boutique_filter') : "";
                $this->dateFrom_filter = $request->request->has('date_from_filter') ? $request->request->get('date_from_filter') : "";
                $this->dateTo_filter = $request->request->has('date_to_filter') ? $request->request->get('date_to_filter') : "";
                $this->etat_filter = $request->request->has('etat_filter') ? $request->request->get('etat_filter') : "";
                $iDisplayLength = intval($request->request->get('length'));
                $iDisplayStart = intval($request->request->get('start'));
                //-----Source -------
                $iTotalRecords = count($offres);
                // filtering
                $offres = $this->handleResults($offres, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                $iFilteredRecords = count($offres);
                //------------------------------------
                //$id = 0; // identity of rows in the table
                /** @var Offre $offre */
                foreach ($offres as $offre) {
                    //$id += 1;
                    //$session->set('offre_'.$id, $offre->getId()); //convert id before sending them to client
                    array_push($json['items'], array(
                        'id' => $offre->getId(),
                        'code' => $offre->getCode(),
                        'designation' => $offre->getDesignation(),
                        'description' => $offre->getDescription(),
                        'image' => $offre->getImage()
                    ));
                }
                $json['totalRecords'] = $iTotalRecords;
                $json['filteredRecords'] = $iFilteredRecords; //nbre d'unité
                return $this->json(json_encode($json), 200);
            }
        } catch (AccessDeniedException $ads) {
            $session->getFlashBag()->add('danger', "<strong>Action interdite!</strong><br/>Pour jouir de ce service, veuillez consulter nos administrateurs.");
            return $this->json($json);
        }
        $session->set('previous_location', $request->getUri());
        return $this->render('APMVenteBundle:offre:index_ajax.html.twig', array(
            'offres' => $offres,
            'boutique' => $boutique,
            'categorie' => $categorie,
            'vendeur' => $vendeur,
            'form' => $form->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'img'),
        ));
    }

    /**
     * @param Boutique $boutique
     */
    private
    function listAndShowSecurity($boutique = null)
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', $user = $this->getUser(), 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    private
    function adminSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$this->getUser() instanceof Admin) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $offres
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private
    function handleResults($offres, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($offres === null) return array();

        if ($this->code_filter != null) {
            $offres = $offres->filter(function ($e) {//filtrage select
                /** @var Offre $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->etat_filter != null) {
            $offres = $offres->filter(function ($e) {//filtrage select
                /** @var Offre $e */
                return $e->getEtat() === intval($this->etat_filter);
            });
        }
        if ($this->dateFrom_filter != null) {
            $offres = $offres->filter(function ($e) {//start date
                /** @var Offre $e */
                $dt1 = (new \DateTime($e->getUpdatedAt()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $offres = $offres->filter(function ($e) {//end date
                /** @var Offre $e */
                $dt = (new \DateTime($e->getUpdatedAt()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->boutique_filter != null) {
            $offres = $offres->filter(function ($e) {//filter with the begining of the entering word
                /** @var Offre $e */
                $str1 = $e->getBoutique()->getDesignation();
                $str2 = $this->boutique_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->designation_filter != null) {
            $offres = $offres->filter(function ($e) {//search for occurences in the text
                /** @var Offre $e */
                $subject = $e->getDesignation();
                $pattern = $this->designation_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        $offres = ($offres !== null) ? $offres->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $offres, function ($e1, $e2) {
            /**
             * @var Offre $e1
             * @var Offre $e2
             */
            $dt1 = $e1->getUpdatedAt()->getTimestamp();
            $dt2 = $e2->getUpdatedAt()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $offres = array_slice($offres, $iDisplayStart, $iDisplayLength, true);

        return $offres;
    }

    /**
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Session $session */
        $session = $request->getSession();
        /** @var Offre $offre */
        $offre = TradeFactory::getTradeProvider('offre');
        $form = $this->createForm('APM\VenteBundle\Form\OffreType', $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->createSecurity($offre->getBoutique(), $offre->getCategorie());
                $em = $this->getDoctrine()->getManager();
                $offre->setVendeur($this->getUser());
                $em->persist($offre);
                $em->flush();
                if ($request->isXmlHttpRequest()) {
                    $json["item"] = array(//permet au client de différencier les nouveaux éléments des élements juste modifiés
                        "action" => 0,
                        "id" => null, //it is null because the table will be reload automatically
                    );
                    $session->getFlashBag()->add('success', "<strong> Création de l'Offre. réf:" . $offre->getCode() . "</strong><br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                    //--------------
                }
                $this->get('apm_core.crop_image')->liipImageResolver($offre->getImage());//resouds tout en créant l'image
                if (null !== $offre->getImage()) {
                    return $this->redirectToRoute('apm_vente_offre_show-image', array('id' => $offre->getId()));
                } else {
                    return $this->redirectToRoute('apm_vente_offre_show', array('id' => $offre->getId()));
                }
                //---
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
        return $this->render('APMVenteBundle:offre:new.html.twig', array(
            'form' => $form->createView(),
            'offre' => $offre,
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'img'),
        ));
    }

    /**
     * @param Categorie $categorie
     * @param Boutique $boutique
     */
    private
    function createSecurity($boutique = null, $categorie = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$this->getUser() instanceof Utilisateur_avm) {
            throw $this->createAccessDeniedException();
        }
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        //Autoriser l'accès à la boutique uniquement au gerant et au proprietaire
        if ($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
            if ($categorie) {//Inserer l'offre uniquement dans la meme boutique que la categorie
                $currentBoutique = $categorie->getBoutique();
                if ($currentBoutique !== $boutique) {
                    throw $this->createAccessDeniedException();
                }
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Tout utilisateur AVM peut voir une offre
     * @param Request $request
     * @param Offre $offre
     * @return Response
     */
    public
    function showImageAction(Request $request, Offre $offre)
    {
        $this->listAndShowSecurity();
        $form = $this->createCrobForm($offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('apm_core.crop_image')->setCropParameters(intval($_POST['x']), intval($_POST['y']), intval($_POST['w']), intval($_POST['h']), $offre->getImage(), $offre);

            return $this->redirectToRoute('apm_vente_offre_show', array('id' => $offre->getId()));
        }

        return $this->render('APMVenteBundle:offre:image.html.twig', array(
            'offre' => $offre,
            'crop_form' => $form->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    private
    function createCrobForm(Offre $offre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_offre_show-image', array('id' => $offre->getId())))
            ->setMethod('POST')
            ->getForm();
    }

    /**
     * Tout utilisateur AVM peut voir une offre
     * @param Request $request
     * @param Offre $offre
     * @return Response | JsonResponse
     */
    public
    function showAction(Request $request, Offre $offre)
    {
        $this->listAndShowSecurity();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'designation' => $offre->getDesignation(),
                'code' => $offre->getCode(),
                'categorie' => $offre->getCategorie()->getId(),
                'boutiqueID' => !$offre->getBoutique() ? -1 : $offre->getBoutique()->getId(),
                'dureeGarantie' => $offre->getDureeGarantie(),
                'remiseProduit' => $offre->getRemiseProduit(),
                'modeVente' => $offre->getModeVente(),
                'modelDeSerie' => $offre->getModelDeSerie(),
                'prixUnitaire' => $offre->getPrixUnitaire(),
                'quantite' => $offre->getQuantite(),
                'unite' => $offre->getUnite(),
                'rate' => $offre->getEvaluation(),
                'typeOffre' => $offre->getTypeOffre(),
                'description' => $offre->getDescription(),
                'publiable' => $offre->getPubliable(),
                'apparenceNeuf' => $offre->getApparenceNeuf(),
                'etat' => $offre->getEtat(),
                'retourne' => $offre->getRetourne(),
                'updatedAt' => $offre->getUpdatedAt()->format("d/m/Y - H:i"),
                'dateCreation' => $offre->getDateCreation() ? $offre->getDateCreation()->format("d/m/Y - H:i") : '',
                'dateExpiration' => $offre->getDateExpiration() ? $offre->getDateExpiration()->format("d/m/Y - H:i") : '',
                'vendeur' => $offre->getVendeur()->getId(),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($offre);
        return $this->render('APMVenteBundle:offre:show.html.twig', array(
            'offre' => $offre,
            'delete_form' => $deleteForm->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

//------------------------ End INDEX ACTION --------------------------------------------

    /**
     * Creates a form to delete a Offre entity.
     *
     * @param Offre $offre The Offre entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private
    function createDeleteForm(Offre $offre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_offre_delete', array('id' => $offre->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Offre entity.
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public
    function editAction(Request $request, Offre $offre)
    {
        $this->editAndDeleteSecurity($offre);
        /** @var Session $session */
        $session = $request->getSession();
        $deleteForm = $this->createDeleteForm($offre);
        $editForm = $this->createForm('APM\VenteBundle\Form\OffreType', $offre);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()
            || $request->isXmlHttpRequest() && $request->getMethod() === "POST"
        ) {
            try {
                $em = $this->getDoctrine()->getManager();
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json["item"] = array();
                    $property = $request->request->get('name');
                    $value = $request->request->get('value');
                    switch ($property) {
                        case 'etat':
                            $offre->setEtat($value);
                            break;
                        case 'designation':
                            $offre->setDesignation($value);
                            break;
                        case 'categorie':
                            $categorie = $em->getRepository('APMVenteBundle:Categorie')->find($value);
                            $offre->setCategorie($categorie);
                            break;
                        case 'publiable':
                            $offre->setPubliable($value);
                            break;
                        case 'apparenceNeuf':
                            $offre->setApparenceNeuf($value);
                            break;
                        case 'modeVente' :
                            $offre->setModeVente($value);
                            break;
                        case 'typeOffre' :
                            $offre->setTypeOffre($value);
                            break;
                        case 'description':
                            $offre->setDescription($value);
                            break;
                        case 'dateExpiration':
                            $offre->setDateExpiration($value);
                            break;
                        case 'dureeGarantie':
                            $offre->setDureeGarantie($value);
                            break;
                        case 'modelDeSerie':
                            $offre->setModelDeSerie($value);
                            break;
                        case 'prixUnitaire':
                            $offre->setPrixUnitaire($value);
                            break;
                        case 'quantite':
                            $offre->setQuantite($value);
                            break;
                        case 'remiseProduit':
                            $offre->setRemiseProduit($value);
                            break;
                        case 'unite':
                            $offre->setUnite($value);
                            break;
                        default:
                            $session->getFlashBag()->add('info', "<strong> Aucune mise à jour effectuée</strong>");
                            return $this->json(json_encode(["item" => null]), 205);
                    }
                    $em->flush();
                    $session->getFlashBag()->add('success', "Mise à jour propriété : <strong>" . $property . "</strong> réf. offre :" . $offre->getCode() . "<br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                $em->persist($offre);
                $em->flush();
                if (null !== $offre->getImage()) {
                    return $this->redirectToRoute('apm_vente_offre_show-image', array('id' => $offre->getId()));
                } else {
                    return $this->redirectToRoute('apm_vente_offre_show', array('id' => $offre->getId()));
                }
                //---
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "Echec de la Modification <br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (RuntimeException $rte) {
                $session->getFlashBag()->add('danger', "Echec de la Modification <br>L'enregistrement a échoué. bien vouloir réessayer plutard, svp!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "Action interdite!<br>Vous n'êtes pas autorisés à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        return $this->render('APMVenteBundle:offre:edit.html.twig', array(
            'offre' => $offre,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    /**
     * @param Offre $offre
     */
    private
    function editAndDeleteSecurity($offre = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$this->getUser() instanceof Utilisateur_avm) {
            throw $this->createAccessDeniedException();
        }
        if (null !== $offre) {
            $boutique = $offre->getBoutique();
            $user = $this->getUser();
            if (null !== $boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
            }
            if ($user !== $offre->getVendeur() && $user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Deletes an Offre entity.
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     */
    public
    function deleteAction(Request $request, Offre $offre = null)
    {
        $this->editAndDeleteSecurity($offre);
        /** @var Session $session */
        $session = $request->getSession();
        try {
            $em = $this->getDoctrine()->getManager();
            if ($request->isXmlHttpRequest()) {
                /** @var Offre $offre */
                $items = $request->request->get('items');
                $elements = json_decode($items);
                $json = null;
                $j = 0;
                $count = count($elements);
                for ($i = 0; $i < $count; $i++) {
                    $offre = null;
                    $id = $elements[$i];
                    $offre = $em->getRepository('APMVenteBundle:Offre')->find($id);
                    if (null !== $offre) {
                        $this->editAndDeleteSecurity($offre);
                        $em->remove($offre);
                        $em->flush();
                        $json[] = $id;//$session->get('offre_' . $id);
                        $j++;
                    }
                }
                $json = json_encode(['ids' => $json, 'action' => 3]);
                $session->getFlashBag()->add('danger', "<strong>" . $j . "</strong> Element(s) supprimé(s)<br> Opération effectuée avec succès!");
                return $this->json($json);
            }
            $em->remove($offre);
            $em->flush();
            return $this->redirectToRoute('apm_vente_offre_index');
        } catch (AccessDeniedException $ads) {
            $session->getFlashBag()->add('danger', "<strong>Action interdite!</strong><br/>Pour jouir de ce service, veuillez consulter nos administrateurs.");
            return $this->json(json_encode(["ids" => null]));
        } catch (ConstraintViolationException $cve) {
            $session->getFlashBag()->add('danger', "<strong>Echec de la suppression</strong><br> <b>" . ($j) . " element(s) supprimé(s); " . ($count - $j) . " échoué(s) </b>, il se peut qu'une d'elle soit utilisée par d'autre ressource");
            return $this->json(json_encode(['ids' => $json, 'action' => 3]));
        }
    }

    public
    function ImageLoaderAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $this->listAndShowSecurity();
            $session = $this->get('session');
            $id = $request->request->get('oData');
            $id = $session->get('offre_' . $id);
            $em = $this->getDoctrine()->getManager();
            $offre = null;
            if (is_numeric($id))
                /** @var Offre $offre */
                $offre = $em->getRepository('APMVenteBundle:Offre')->find($id);
            if ($offre !== null) {
                /** @var Offre $offre */
                //reception du fichier sur le serveur et stockage via vich
                try {
                    $this->editAndDeleteSecurity($offre);
                    /*tester et stocker le fichier reçu sur le serveur*/
                    if (isset($_FILES["myFile"])) {
                        $file = $_FILES["myFile"];
                    } else {
                        $session->getFlashBag()->add('danger', "<strong>Aucun fichier chargé.</strong><br> Veuillez réessayez l'opération avec un fichier valide");
                        return $this->json(json_encode(["item" => null]));
                    }
                    //test de validité du type de fichier
                    if (preg_match('/^image\/*?/i', $file['type']) !== 1) {
                        $session->getFlashBag()->add('danger', "<strong>Fichier Invalide.</strong><br>Vous devez charger une image valide!");
                        return $this->json(json_encode(["item" => null]));
                    }
                    //générer un nom unique pour le fichier
                    $path = $this->getParameter('images_url') . '/' . $file['name'];
                    if (!move_uploaded_file($file['tmp_name'], $path)) {
                        $session->getFlashBag()->add('danger', "<strong>L'opération a échoué.</strong><br>Aucune image chargée. Veuillez réessayez!");
                        return $this->json(json_encode(["item" => null]));
                    }
                    /** @var File_NAME $file */
                    $file = new File($path);
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move($this->getParameter('images_url'), $fileName); // renomer le fichier
                    $offre->setImage($fileName);
                    $em->flush();
                    $session->getFlashBag()->add('success', "Image mise à jour de l\'offre :<strong>" . $offre->getCode() . "</strong><br>");
                    //traitement du fichier puis stockage
                    $this->get('apm_core.crop_image')->setCropParameters(intval($_POST['x']), intval($_POST['y']), intval($_POST['w']), intval($_POST['h']), $offre->getImage(), $offre);
                    $json["item"] = array(//permet au client de différencier les nouveaux éléments des élements juste modifiés
                        "isNew" => false,
                        "isImage" => true,
                        "id" => $id,
                    );
                    $this->get('apm_core.crop_image')->liipImageResolver($offre->getImage());//resolution et deplacement de l'images dans media/

                    return $this->json(json_encode($json));
                } catch (ConstraintViolationException $e) {
                    $session->getFlashBag()->add('danger', "<strong> Echec de la suppression </strong><br>La suppression a échouée due à une contrainte de données!");
                    return $this->json(json_encode(["item" => null]));
                } catch (AccessDeniedException $ads) {
                    $session->getFlashBag()->add('danger', "Action interdite!<br>Vous n'êtes pas autorisés à effectuer cette opération!");
                    return $this->json(json_encode(["item" => null]));
                } catch (RuntimeException $rte) {
                    $session->getFlashBag()->add('danger', "<strong>Echec de l'opération.</strong><br>L'enregistrement a échoué. bien vouloir réessayer plutard, svp!");
                    return $this->json(json_encode(["item" => null]));
                }
            }
            $session->getFlashBag()->add('info', "<strong>Aucune entité pour l'image</strong><br> Veuillez crééer une offre !");
            return $this->json(json_encode(["item" => null]));

        }
        return new JsonResponse();
    }

}
