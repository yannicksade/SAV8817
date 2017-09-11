<?php

namespace APM\VenteBundle\Controller;

use APM\UserBundle\Entity\Utilisateur_avm;
use function Couchbase\defaultDecoder;
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
use APM\CoreBundle\Form\Type\FilterFormType;


/**
 * Offre controller.
 *
 */
class OffreController extends Controller
{
    private $value;

    /**
     * @ParamConverter("categorie", options={"mapping": {"categorie_id":"id"}})
     * Liste les offres de la boutique ou du vendeur
     * @param Request $request
     * @param Boutique $boutique
     * @param Categorie $categorie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, Boutique $boutique = null, Categorie $categorie = null)
    {
        $vendeur = null;
        if (null !== $boutique) {
            $this->listAndShowSecurity($boutique);
            if ($categorie) {
                $offres = $categorie->getOffres();
            } else {
                $offres = $boutique->getOffres();
            }
        } else {

            $this->listAndShowSecurity();
            $user = $this->getUser();
            /** @var Collection $offres */
            $offres = $user->getOffres();
            /** @var Offre $anOffer */
            $anOffer = $offres->offsetGet(0);
            if ($anOffer) $vendeur = $anOffer->getVendeur();
        }
        //-------------------------------------------------------------
        $filter = $this->createForm(FilterFormType::class);
        $filter->handleRequest($request);
        if ($filter->isSubmitted() && $filter->isValid()) {
            $this->value = $filter->get('filter')->getData();
            $offres = $offres->filter(function ($offre) {//filtrage
                /** @var Offre $offre */
                return $offre->getDesignation() == $this->value;
            });
        }
        $offres = $offres->slice(0, 10); // slice de pagination
        //--------------------------------------------------------------
        return $this->render('APMVenteBundle:offre:index.html.twig', array(
            'filter' => $filter->createView(),
            'offres' => $offres,
            'boutique' => $boutique,
            'categorie' => $categorie,
            'vendeur' => $vendeur,
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'img'),
        ));
    }


    /**
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Offre $offre */
        $offre = TradeFactory::getTradeProvider('offre');
        $offre->setVendeur($this->getUser());
        $form = $this->createForm('APM\VenteBundle\Form\OffreType', $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($offre->getBoutique(), $offre->getCategorie());
            $em = $this->getDoctrine()->getManager();
            $em->persist($offre);
            $em->flush();
            $this->get('apm_core.crop_image')->liipImageResolver($offre->getImage());//resouds tout en créant l'image
            //---
            $dist = dirname(__DIR__, 4);
            $file = $dist . '/web/' . $this->getParameter('images_url') . '/' . $offre->getImage();
            if (file_exists($file)) {
                return $this->redirectToRoute('apm_vente_offre_show-image', array('id' => $offre->getId()));
            } else {
                return $this->redirectToRoute('apm_vente_offre_show', array('id' => $offre->getId()));
            }
            //---
        }
        return $this->render('APMVenteBundle:offre:new.html.twig', array(
            'form' => $form->createView(),
            'offre' => $offre,
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'img'),
        ));
    }

    /**
     * Tout utilisateur AVM peut voir une offre
     * @param Request $request
     * @param Offre $offre
     * @return Response
     */
    public function showImageAction(Request $request, Offre $offre)
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

    private function createCrobForm(Offre $offre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_offre_show-image', array('id' => $offre->getId())))
            ->setMethod('POST')
            ->getForm();
    }

    /**
     * Tout utilisateur AVM peut voir une offre
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Offre $offre)
    {
        $this->listAndShowSecurity();
        $deleteForm = $this->createDeleteForm($offre);

        return $this->render('APMVenteBundle:offre:show.html.twig', array(
            'offre' => $offre,
            'delete_form' => $deleteForm->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    /**
     * Creates a form to delete a Offre entity.
     *
     * @param Offre $offre The Offre entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Offre $offre)
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Offre $offre)
    {
        $this->editAndDeleteSecurity($offre);
        $deleteForm = $this->createDeleteForm($offre);
        $editForm = $this->createForm('APM\VenteBundle\Form\OffreType', $offre);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->createSecurity();
            $em = $this->getDoctrine()->getManager();
            $em->persist($offre);
            $em->flush();

            //---
            $dist = dirname(__DIR__, 4);
            $file = $dist . '/web/' . $this->getParameter('images_url') . '/' . $offre->getImage();
            if (file_exists($file)) {
                return $this->redirectToRoute('apm_vente_offre_show-image', array('id' => $offre->getId()));
            } else {
                return $this->redirectToRoute('apm_vente_offre_show', array('id' => $offre->getId()));
            }
            //---
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
    private function editAndDeleteSecurity($offre)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $boutique = $offre->getBoutique();
        if ($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Deletes a Offre entity.
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Offre $offre)
    {
        $this->editAndDeleteSecurity($offre);
        $form = $this->createDeleteForm($offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            $em = $this->getDoctrine()->getManager();
            $em->remove($offre);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_offre_index');
    }

    public function deleteFromListAction(Offre $offre)
    {
        $this->editAndDeleteSecurity($offre);

        $em = $this->getDoctrine()->getManager();
        $em->remove($offre);
        $em->flush();

        return $this->redirectToRoute('apm_vente_offre_index');
    }


    /********************************************AJAX REQUEST********************************************/

    private $desc_filter;
    private $boutique_filter;
    private $code_filter;
    private $etat_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $record_filter;
    private $status_list = array(
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

    public function ImageLoaderAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $this->listAndShowSecurity();
            $session = $this->get('session');
            $id = intval($request->request->get('oData'));
            $em = $this->getDoctrine()->getManager();
            $offre = null;
            if (is_numeric($id))
                $offre = $em->getRepository('APMVenteBundle:Offre')->find($id);
            if ($offre !== null) {
                /** @var Offre $offre */
                //reception du fichier sur le serveur et stockage via vich
                try {
                    $user = $this->getUser();
                    $gerant = null;
                    $proprietaire = null;
                    $boutique = $offre->getBoutique();
                    if (null !== $boutique) {
                        $gerant = $boutique->getGerant();
                        $proprietaire = $boutique->getProprietaire();
                    }
                    $vendeur = $offre->getVendeur();
                    $this->editAndDeleteAjaxSecurity($user, $gerant, $proprietaire, $vendeur);
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
                        "id" => $offre->getId(),
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

    /**
     * Create
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function indexAjaxAction(Request $request)
    {
        //---------------------------post------------------------
        $form = $this->createForm('APM\VenteBundle\Form\OffreType');
        $form->handleRequest($request);
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST" && $form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $session = $this->get('session');
            try { // valider  le formulaire ici
                // create a new element
                $json['item'] = array();
                //---create security 1------//
                $this->createSecurity();
                $data = $request->request->get('offre');
                /** @var Offre $offre */
                $offre = TradeFactory::getTradeProvider("offre");
                if (null !== $offre) {
                    $categorie = null;
                    $boutique = null;
                    if (isset($data['boutique'])) {
                        $boutique = $em->getRepository('APMVenteBundle:Boutique')->find($data['boutique']);
                        $offre->setBoutique($boutique);
                        $this->createSecurity($boutique, null);
                    }
                    if (isset($data['categorie'])) {
                        $categorie = $em->getRepository('APMVenteBundle:Categorie')->find($data['categorie']);
                        $offre->setCategorie($categorie);
                        $this->createSecurity($boutique, $categorie);
                    }
                    //---create security 2------//
                    $offre->setVendeur($this->getUser());

                    $offre->setDesignation($data['designation']);
                    if (isset($data['etat'])) $offre->setEtat($data['etat']);
                    $offre->setQuantite($data['quantite']);
                    if (isset($data['description'])) $offre->setDescription($data['description']);
                    $offre->setRemiseProduit($data['remiseProduit']);
                    $offre->setPrixunitaire($data['prixUnitaire']);
                    $offre->setModelDeSerie($data['modelDeSerie']);
                    if (isset($data['modeVente'])) $offre->setModeVente($data['modeVente']);
                    if (isset($data['typeOffre'])) $offre->setTypeOffre($data['typeOffre']);
                    if (isset($data['publiable'])) $offre->setPubliable($data['publiable']);
                    if (isset($data['apparenceNeuf'])) $offre->setApparenceNeuf($data['apparenceNeuf']);
                    if (isset($data['credit'])) $offre->setCredit($data['credit']); // variable à enregistrement parallèlement dans par un formulaire distinct: dit varible optionnelles comme les autres
                    $offre->setDureeGarantie($data['dureeGarantie']);
                    if (isset($data['imageFile'])) $offre->setImage($data['imageFile']);
                    $em->persist($offre);
                    $em->flush();
                    $json["item"] = array(//permet au client de différencier les nouveaux éléments des élements juste modifiés
                        "action" => 0,
                        "id" => null, //it is null because the table will be reload automatically
                    );
                    $session->getFlashBag()->add('success', "<strong> Création de l'Offre. réf:" . $offre->getCode() . "</strong><br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json));
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
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        //------------------ Form---------------
        /*$image_form = $this->createForm(ImageType::class);*/
        return $this->render('APMVenteBundle:offre:index_ajax.html.twig', array(
            // 'image_form'=>$image_form->createView(),
            'form' => $form->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));

    }

    //Liste tous les Offres
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
                $this->record_filter = $request->request->has('record_filter') ? $request->request->get('record_filter') : "";
                $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
                $this->desc_filter = $request->request->has('desc_filter') ? $request->request->get('desc_filter') : "";
                $this->boutique_filter = $request->request->has('boutique_filter') ? $request->request->get('boutique_filter') : "";
                $this->dateFrom_filter = $request->request->has('date_from_filter') ? $request->request->get('date_from_filter') : "";
                $this->dateTo_filter = $request->request->has('date_to_filter') ? $request->request->get('date_to_filter') : "";
                $this->etat_filter = $request->request->has('etat_filter') ? $request->request->get('etat_filter') : "";
                $iDisplayLength = intval($request->request->get('length'));
                $iDisplayStart = intval($request->request->get('start'));
                $sEcho = intval($request->request->get('draw'));

                //-----Source -------
                /** @var Utilisateur_avm $user */
                $user = $this->getUser();
                $offres = $user->getOffres();
                //page paremeters
                $iTotalRecords = count($offres); // counting
                $offres = $this->elementsFilter($offres); // filtering
                $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
                //------ filtering and paging -----
                usort(//assortment: descending of date -- du plus recent au plus ancient
                    $offres, function ($e1, $e2) {
                    /**
                     * @var Offre $e1
                     * @var Offre $e2
                     */
                    $dt1 = $e1->getUpdatedAt()->getTimestamp();
                    $dt2 = $e2->getUpdatedAt()->getTimestamp();
                    return $dt1 <= $dt2 ? 1 : -1;
                });
                $offres = array_slice($offres, $iDisplayStart, $iDisplayLength, true); //slicing, preserve the keys' order

                //------------------------------------
                $id = 0; // identity of rows in the table
                /** @var Offre $offre */
                foreach ($offres as $offre) {
                    $id += 1;
                    $etat = $offre->getEtat();
                    $boutique = $offre->getBoutique();
                    $aboutiqueRoute = "<i>free lance</i>";
                    if (null !== $boutique) $aboutiqueRoute = '<a href="../boutique/' . $boutique->getId() . '/show">' . $boutique . '</a>';
                    $updatedAt = $offre->getUpdatedAt()->format("d/m/Y - H:i");
                    $records['data'][] = array(
                        '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">' . $id . ' <input value="' . $offre->getId() . '" name="_id[]" type="checkbox" class="checkboxes"/><span></span></label>',
                        '<span><a  href="' . $offre->getId() . '/show">' . $offre->getCode() . '</a></span>',
                        '<span>' . $offre . '</span>',
                        '<span>' . $aboutiqueRoute . '</span>',
                        '<span>' . $updatedAt . '</span>',
                        '<span class="label label-sm label-' . (key($this->status_list[$etat])) . '" >' . (current($this->status_list[$etat])) . '</span>'
                    );
                }
                $records['draw'] = $sEcho;
                $records["recordsTotal"] = $iTotalRecords;
                $records["recordsFiltered"] = $iTotalRecords;
                return $this->json($records);
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite!</strong><br/>Pour jouir de ce service, veuillez consulter nos administrateurs.");
                return $this->json($records);
            } catch (RuntimeException $rte) {
                $session->getFlashBag()->add('danger', "<strong>Echec de chargement </strong><br>Une erreur systeme s'est produite. bien vouloir réessayer plutard, svp!");
                return $this->json(json_encode(["item" => null]));
            }
        }

        return new JsonResponse('name');
    }

    public function deleteAjaxAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {

            $session = $this->get('session');
            try {
                $this->editAndDeleteAjaxSecurity();
                /** @var Offre $offre */
                $items = $request->request->get('items');
                $elements = json_decode($items);
                $em = $this->getDoctrine()->getManager();
                $json = null;
                $j = 0;
                $count = count($elements);
                for ($i = 0; $i < $count; $i++) {
                    $offre = null;
                    $id = intval($elements[$i]);
                    if (is_numeric($id)) $offre = $em->getRepository('APMVenteBundle:Offre')->find($id);
                    if ($offre !== null) {
                        //---- Secutity 2 : allow only the autor to delete -------
                        $boutique = $offre->getBoutique();
                        $gerant = null;
                        $proprietaire = null;
                        if (null !== $boutique) {
                            $gerant = $boutique->getGerant();
                            $proprietaire = $boutique->getProprietaire();
                        }
                        $this->editAndDeleteAjaxSecurity($this->getUser(), $gerant, $proprietaire, $offre->getVendeur());
                        $em->remove($offre);
                        $em->flush();
                        $json[] = $id;
                        $j++;
                    }
                }
                $json = json_encode(['ids' => $json, 'action' => 3]);
                $session->getFlashBag()->add('danger', "<strong>" . $j . "</strong> Element(s) supprimé(s)<br> Opération effectuée avec succès!");
                return $this->json($json);
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite!</strong><br/>Pour jouir de ce service, veuillez consulter nos administrateurs.");
                return $this->json(json_encode(["ids" => null]));
            } catch (RuntimeException $rte) {
                $session->getFlashBag()->add('danger', "<strong>Echec de la suppression </strong><br>Une erreur systeme s'est produite. bien vouloir réessayer plutard, svp!");
                return $this->json(json_encode(["ids" => null]));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de la suppression</strong><br> <b>" . ($j) . " element(s) supprimé(s); " . ($count - $j) . " échoué(s) </b>, il se peut qu'une d'elle soit utilisée par d'autre ressource");
                return $this->json(json_encode(['ids' => $json, 'action' => 3]));
            }
        }
        return new JsonResponse();
    }

    /*
     * Update an offer
     */
    public function handleOffreAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) return new JsonResponse();
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $json['item'] = array();
        /** @var Offre $offre */
        $offre = null;
        $m_request = $request->query->get('method');
        try {
            if (isset($m_request) && $m_request === "get") {//get data with id through put method
                $this->listAndShowSecurity();
                $item = $request->request->get('item');
                $item = json_decode($item);
                $id = $item[0];
                $id = intval($id);
                if (is_numeric($id)) $offre = $em->getRepository('APMVenteBundle:Offre')->find($id);
                if (null === $offre) return new JsonResponse();
                $mode_vente = array(
                    "Vente Normale", "Vente aux enchères", "Vente en solde", "Vente restreinte"
                );
                $type_offre = array(
                    "Article", "Produit", "Service",
                );
                $json['item'] = array(
                    'designation' => $offre->getDesignation(),
                    'code' => $offre->getCode(),
                    'categorie' => $offre->getCategorie() ? $offre->getCategorie()->getDesignation() : '',
                    'boutique' => $offre->getBoutique() ? $offre->getBoutique()->getDesignation() : '-',
                    'dureeGarantie' => $offre->getDureeGarantie(),
                    'remise' => $offre->getRemiseProduit(),
                    'modeVente' => $mode_vente[$offre->getModeVente()],
                    'modelDeSerie' => $offre->getModelDeSerie(),
                    'prixUnitaire' => $offre->getPrixUnitaire(),
                    'quantite' => $offre->getQuantite(),
                    'unite' => $offre->getUnite(),
                    'rate' => $offre->getEvaluation(),
                    'type' => $type_offre[$offre->getTypeOffre()],
                    'description' => $offre->getDescription(),
                    'publiable' => $offre->getPubliable() ? "No" : "Yes",
                    'apparence' => $offre->getApparenceNeuf() ? "Neuf" : "Occasion",
                    'etat' => current($this->status_list[$offre->getEtat()]),
                    'retourne' => $offre->getRetourne() ? "Oui" : "Non",
                    'updatedAt' => $offre->getUpdatedAt()->format("d/m/Y - H:i"),
                    'dateCreation' => $offre->getDateCreation() ? $offre->getDateCreation()->format("d/m/Y - H:i") : '',
                    'dateExpiration' => $offre->getDateExpiration() ? $offre->getDateExpiration()->format("d/m/Y - H:i") : '',
                    'vendeur' => $offre->getVendeur()->getUsername(),
                );
                return $this->json(json_encode($json));

            }
            elseif (isset($m_request) && $m_request === "post") {//individual property edit
                //autorisation d'accès
                $this->editAndDeleteAjaxSecurity();
                $pk = $request->request->get('pk');
                $id = intval($pk);
                if (is_numeric($id)) $offre = $em->getRepository('APMVenteBundle:Offre')->find($id);
                if (null === $offre) return new JsonResponse();

                //***---double vérification à l'aide du code ---
                /*if ($data['code'] !== $offre->getCode()) {
                    $session->getFlashBag()->add('danger', "Action interdite!<br>Vous n'êtes pas autorisés à effectuer cette opération!");
                    return $this->json(json_encode(["item" => null]));
                }*/
                //***-----------------------------------------
                $user = $this->getUser();
                $gerant = null;
                $proprietaire = null;
                $boutique = $offre->getBoutique();
                if (null !== $boutique) {
                    $gerant = $boutique->getGerant();
                    $proprietaire = $boutique->getProprietaire();
                }
                $vendeur = $offre->getVendeur();
                $this->editAndDeleteAjaxSecurity($user, $gerant, $proprietaire, $vendeur);
                //------------------ security: allow only the seller, the manager and the owner of the product-----------------------
                if ($user === $gerant || $user === $proprietaire || $user === $vendeur) { //permettra de scinder les traitements et mises à jour des attributs selon les ayant-droits
                    $property = $request->request->get('name');
                    $value = $request->request->get('value');
                    switch ($property) {
                        case 'etat':
                            $offre->setEtat($value);
                            $json["item"] = array(//pour actualiser la table
                                "action" => 1,
                            );
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
                        case 'designation':
                            $offre->setDesignation($value);
                            $json["item"] = array(//pour actualiser la table
                                "action" => 1,
                            );
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
                            $session->getFlashBag()->add('info', "<strong> Aucune modification</strong>");
                            return $this->json(json_encode(["item" => null]));
                    }

                    //----------------------------------------------------------------------------------------
                    //-------- prepareration de la reponse du vendeur----
                    // préparation de la notification du client
                    $em->flush();
                    $session->getFlashBag()->add('success', "Modification ou Mise à jour de l'offre :<strong>" . $offre->getCode() . "</strong><br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json));
                }
            }
            elseif ($request->getMethod() === "POST") { //multiple property edit

                $session->getFlashBag()->add('success', "Modification ou Mise à jour de l'offre------TEST POST :<strong>" . $offre->getCode() . "</strong><br> Opération effectuée avec succès!");
                return $this->json(json_encode(["item" => null]));
            }
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

        return new JsonResponse();
    }

//------------------------ End INDEX ACTION --------------------------------------------

    /**
     * @param Collection $offres
     * @return array
     */
    private function elementsFilter($offres)
    {
        if ($offres == null) return array();

        if ($this->code_filter != null) {
            $offres = $offres->filter(function ($e) {//filtrage select
                /** @var Offre $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->etat_filter != null) {
            $offres = $offres->filter(function ($e) {//filtrage select
                /** @var Offre $e */
                return $e->getEtat() === $this->etat_filter;
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
        if ($this->desc_filter != null) {
            $offres = $offres->filter(function ($e) {//search for occurences in the text
                /** @var Offre $e */
                $subject = $e->getDesignation();
                $pattern = $this->desc_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        return ($offres != null) ? $offres->toArray() : [];
    }

    /**
     * @param Categorie $categorie
     * @param Boutique $boutique
     */
    private function createSecurity($boutique = null, $categorie = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
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
     * @param Boutique $boutique
     */
    private function listAndShowSecurity($boutique = null)
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    private function editAndDeleteAjaxSecurity($user = null, $gerant = null, $proprietaire = null, $vendeur = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        if ($user !== $gerant && $user !== $proprietaire && $user !== $vendeur) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }
}
