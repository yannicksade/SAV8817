<?php

namespace APM\VenteBundle\Controller;

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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Offre controller.
 *
 */
class OffreController extends Controller
{

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
            $this->get('apm_core.crop_image')->liipImageResolver($offre->getImage());//resouds tout en créant l'images
            //---
            $dist = dirname(__DIR__, 4);
            $file = $dist . '/web/' . $this->getParameter('images_url') . '/' . $offre->getImage();

            if (file_exists($file)) {
                return $this->redirectToRoute('apm_vente_offre_show-images', array('id' => $offre->getId()));
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

    public function ImageLoaderAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $this->listAndShowSecurity();
            $session = $this->get('session');
            $id = intval($request->query->get('id'));
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
                    $this->editAndDeleteSecurity($user, $gerant, $proprietaire, $vendeur);
                    /*tester et stocker le fichier reçu sur le serveur*/
                    if (isset($_FILES["myFile"])) $file = $_FILES["myFile"]; else {
                        $session->getFlashBag()->add('danger', "<strong>Aucun fichier chargé.</strong><br> Veuillez réessayez l'opération avec un fichier valide");
                        return $this->json(json_encode(["item" => null]));
                    }
                    //test de validité du type de fichier
                    if(preg_match('/^image\/*?/i', $file['type']) !== 1){
                        $session->getFlashBag()->add('danger', "<strong>Fichier Invalide.</strong><br>Vous devez charger une image valide!");
                        return $this->json(json_encode(["item" => null]));
                    }
                    //générer un nom unique pour le fichier
                    $path = $this->getParameter('images_url') . '/' . $file['name'];
                    if (!move_uploaded_file($file['tmp_name'], $path)) {
                        $session->getFlashBag()->add('danger', "<strong>L'opération a échoué.</strong><br>Aucune image chargée. Veuillez réessayez!");
                        return $this->json(json_encode(["item" => null]));
                    }
                    $file = new File($path);
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();
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

    private function createCrobForm(Offre $offre)
    {
        return $this->createFormBuilder()
            /*  ->setAction($this->generateUrl('apm_vente_offre_show-images', array('id' => $offre->getId())))*/
            ->setMethod('POST')
            ->getForm();
    }

    /**
     * CR.U.D
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function indexAction(Request $request)
    {
        //---------------------------post------------------------
        $form = $this->createForm('APM\VenteBundle\Form\OffreType');
        $form->handleRequest($request);
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST" && $form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $session = $this->get('session');
            $this->editAndDeleteSecurity();
            /** @var Offre $offre */
            $data = $request->request->get('offre');
            $offre = null;
            $id = intval($data['id']);
            if (is_numeric($id)) $offre = $em->getRepository('APMVenteBundle:Offre')->find($id);
            $json['item'] = array();
            if (null !== $offre) { //Update- Mise à jour
                try {
                    //autorisation d'accès
                    //***---double vérification à l'aide du code ---
                    if ($data['code'] !== $offre->getCode()) {
                        $session->getFlashBag()->add('danger', "Action interdite!<br>Vous n'êtes pas autorisés à effectuer cette opération!");
                        return $this->json(json_encode(["item" => null]));
                    }
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
                    $this->editAndDeleteSecurity($user, $gerant, $proprietaire, $vendeur);
                    //------------------ security: allow only the seller, the manager and the owner of the product-----------------------
                    $description = "undefined";
                    $etat = "undefined";
                    $garantie = "undefined";
                    $designation = "undefined";
                    $dateExpiration = "undefined";
                    $categorie = "undefined";
                    $publiable = "undefined";
                    $apparence = "undefined";
                    $modeVente = "undefined";
                    $modelDeSerie = "undefined";
                    $prixUnitaire = "undefined";
                    $quantite = "undefined";
                    $remise = "undefined";
                    $typeOffre = "undefined";
                    $unite = "undefined";
                    $catID = null;
                    if ($user === $gerant || $user === $proprietaire || $user === $vendeur) { //permettra de scinder les traitements et mises à jour des attributs selon les ayant-droits

                        if (isset($data['etat'])) {
                            $e = $data['etat'];
                            if ($offre->getEtat() != $e) {
                                $etat = $e;
                                $offre->setEtat($etat);
                            }
                        }
                        if (isset($data['categorie'])) {
                            $categorieID = $data['categorie'];
                            $cat = $offre->getCategorie();
                            if (null !== $cat) $catID = $cat->getId();
                            if ($catID != $categorieID) {
                                $categorie = $em->getRepository('APMVenteBundle:Categorie')->find($categorieID);
                                $offre->setCategorie($categorie);
                            }
                        }
                        if (isset($data['publiable'])) {
                            $pub = $data['publiable'];
                            if ($offre->getPubliable() != $pub) {
                                $publiable = $pub;
                                $offre->setPubliable($publiable);
                            }
                        }
                        if (isset($data['apparenceNeuf'])) {
                            $isBrandNew = $data['apparenceNeuf'];
                            if ($offre->getApparenceNeuf() != $isBrandNew) {
                                $apparence = $isBrandNew;
                                $offre->setApparenceNeuf($apparence);
                            }
                        }
                        if (isset($data['modeVente'])) {
                            $mode = $data['modeVente'];
                            if ($offre->getModeVente() != $mode) {
                                $modeVente = $mode;
                                $offre->setModeVente($modeVente);
                            }
                        }
                        if (isset($data['typeOffre'])) {
                            $type = $data['typeOffre'];
                            if ($offre->getTypeOffre() != $type) {
                                $typeOffre = $type;
                                $offre->setTypeOffre($typeOffre);
                            }
                        }
                        if (isset($data['description'])) {
                            $desc = $data['description'];
                            if ($offre->getDescription() != $desc) {
                                $description = $desc;
                                $offre->setDescription($description);
                            }
                        }
                        if (isset($data['dateExpiration'])) {
                            $date = $data['dateExpiration'];
                            if ($offre->getDateExpiration() != $date) {
                                $dateExpiration = $date;
                                $offre->setDateExpiration($dateExpiration);
                            }

                        }
                        $duree = $data['dureeGarantie'];
                        if ($offre->getDureeGarantie() != $duree) {
                            $garantie = $duree;
                            $offre->setDureeGarantie($garantie);
                        }
                        $name = $data['designation'];
                        if ($offre->getDesignation() != $name) {
                            $designation = $name;
                            $offre->setDesignation($designation);
                        }
                        $serialNumber = $data['modelDeSerie'];
                        if ($offre->getModelDeSerie() != $serialNumber) {
                            $modelDeSerie = $serialNumber;
                            $offre->setModelDeSerie($modelDeSerie);
                        }
                        $price = $data['prixUnitaire'];
                        if ($offre->getPrixUnitaire() != $price) {
                            $prixUnitaire = $price;
                            $offre->setPrixUnitaire($prixUnitaire);
                        }
                        $qte = $data['quantite'];
                        if ($offre->getQuantite() != $qte) {
                            $quantite = $qte;
                            $offre->setQuantite($quantite);
                        }
                        $discount = $data['remiseProduit'];
                        if ($offre->getRemiseProduit() != $discount) {
                            $remise = $discount;
                            $offre->setRemiseProduit($remise);
                        }
                        $u = $data['unite'];
                        if ($offre->getUnite() != $u) {
                            $unite = $u;
                            $offre->setUnite($unite);
                        }
                    }
                    //----------------------------------------------------------------------------------------
                    //-------- prepareration de la reponse du vendeur----
                    $json["item"] = array(//permet au client de différencier les mises a jour des nouveaux éléments
                        "action" => 1,
                        "id" => $id,
                    );
                    // préparation de la notification du client
                    if ($publiable === "undefined" || $designation !== "undefined" || $description !== "undefined" || $etat !== "undefined" || $garantie !== "undefined" || $dateExpiration !== "undefined" || $typeOffre !== "undefined"
                        || $remise !== "undefined" || $quantite !== "undefined" || $modelDeSerie !== "undefined" || $apparence !== "undefined" || $prixUnitaire !== "undefined" || $categorie !== "undefined" || $modeVente !== "undefined"
                        || $unite !== "undefined"
                    ) {
                        $em->flush();
                        $session->getFlashBag()->add('success', "Modification ou Mise à jour de l\offre :<strong>" . $offre->getCode() . "</strong><br> Opération effectuée avec succès!");
                        return $this->json(json_encode($json));
                    } else {
                        $session->getFlashBag()->add('info', "<strong> Aucune modification</strong><br> Action non effectuée!");
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
            } else {// create a new element
                try { // valider  le formulaire ici
                    //---create security 1------//
                    $this->createSecurity();
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
        }
        //------------------ Form---------------
        /*$image_form = $this->createForm(ImageType::class);*/
        return $this->render('APMVenteBundle:offre:index.html.twig', array(
            // 'image_form'=>$image_form->createView(),
            'form' => $form->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));

    }

    private $desc_filter;
    private $boutique_filter;
    private $code_filter;
    private $etat_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $record_filter;

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

                $mode_vente = array(
                    "Vente Normale", "Vente aux enchères", "Vente en solde", "Vente restreinte"
                );
                $type_offre = array(
                    "Article", "Produit", "Service",
                );
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
                    $dateExp = null;
                    $dateCreate = $offre->getDateCreation();
                    $dureeGarantie = $offre->getDureeGarantie();
                    $categorieID = null;
                    $boutiqueID = null;
                    $categorie = $offre->getCategorie();
                    if ($categorie) $categorieID = $offre->getCategorie()->getId();
                    $boutique = $offre->getBoutique();
                    if ($boutique) $boutiqueID = $offre->getBoutique()->getId(); else $boutique = "<i>free lance</i>";
                    if (null !== $offre->getDateExpiration()) $dateExp = $offre->getDateExpiration()->format("d/m/Y - H:i");
                    $dateCreate = $dateCreate->format("d/m/Y - H:i");
                    $retourne = $offre->getRetourne() ? "OUI" : "NON";
                    $publier = $offre->getPubliable() ? "OUI" : "NON";
                    $apparence = $offre->getApparenceNeuf();
                    if ($apparence === 1) $apparence = "NEUF"; elseif ($apparence === 0) $apparence = "OCCASION";
                    $records['data'][] = array(
                        '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id_' . $offre->getId() . '" type="checkbox" class="checkboxes"/><span></span><i class="hidden categorie">' . $categorie . '</i><i class="hidden categorieID">' . $categorieID . '</i><i class="hidden boutiqueID">' . $boutiqueID . '</i>
                        <i class="hidden desc">' . $offre->getDescription() . '</i><i class="hidden vendeur">' . $offre->getVendeur()->getUsername() . '</i><i class="hidden vendeurID">' . $offre->getVendeur()->getId() . '</i><i class="hidden credit">' . $offre->getCredit() . '</i><i class="hidden image">' . $offre->getImage() . '</i>
                        <i class="hidden dateExpiration">' . $dateExp . '</i><i class="hidden dateCreation">' . $dateCreate . '</i><i class="hidden dureeGarantie">' . $dureeGarantie . '</i><i class="hidden prix">' . $offre->getPrixUnitaire() . '</i>
                        <i class="hidden publiable">' . $publier . '</i><i class="hidden publiableID">' . $offre->getPubliable() . '</i><i class="hidden retourne">' . $retourne . '</i><i class="hidden retourneID">' . $offre->getRetourne() . '</i><i class="hidden apparence">' . $apparence . '</i><i class="hidden apparenceID">' . $offre->getApparenceNeuf() . '</i><i class="hidden modeVenteID">' . $offre->getModeVente() . '</i><i class="hidden modeVente">' . $mode_vente[$offre->getModeVente()] . '</i>
                        <i class="hidden modelDeSerie">' . $offre->getModelDeSerie() . '</i><i class="hidden unite">' . $offre->getUnite() . '</i><i class="hidden quantite">' . $offre->getQuantite() . '</i><i class="hidden remise">' . $offre->getRemiseProduit() . '</i><i class="hidden rate">' . $offre->getEvaluation() . '</i><i class="hidden type">' . $type_offre[$offre->getTypeOffre()] . '</i><i class="hidden typeID">' . $offre->getTypeOffre() . '</i><i class="hidden dataSheet">' . $offre->getDataSheet() . '</i></label>',
                        '<span><i class="id hidden">' . $offre->getId() . '</i>' . $id . '</span>',
                        '<span class="code">' . $offre->getCode() . '</span>',
                        '<a href="#" class="designation">' . $offre . '</a>',
                        '<a href="#" class="boutique">' . $boutique . '</a>',
                        '<span class="updatedAt">' . $offre->getUpdatedAt()->format("d/m/Y - H:i") . '</span>',
                        '<span class="etat label label-sm label-' . (key($status_list[$etat])) . '"><input type="hidden" value="' . $etat . '"/>' . (current($status_list[$etat])) . '</span>'
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

        return new JsonResponse();
    }

    public function deleteAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {

            $session = $this->get('session');
            try {
                $this->editAndDeleteSecurity();
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
                        $this->editAndDeleteSecurity($this->getUser(), $gerant, $proprietaire, $offre->getVendeur());
                        $em->remove($offre);
                        $em->flush();
                        $json[] = $id;
                        $j++;
                    }
                }
                $json = json_encode(['ids' => $json, 'action'=>3]);
                $session->getFlashBag()->add('danger', "<strong>" . $j . "</strong> Element(s) supprimé(s)<br> Opération effectuée avec succès!");
                return $this->json($json);
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite!</strong><br/>Pour jouir de ce service, veuillez consulter nos administrateurs.");
                return $this->json($records);
            } catch (RuntimeException $rte) {
                $session->getFlashBag()->add('danger', "<strong>Echec de la suppression </strong><br>Une erreur systeme s'est produite. bien vouloir réessayer plutard, svp!");
                return $this->json(json_encode(["item" => null]));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de la suppression Total</strong><br> pour  <b>" . $count . "</b>elements,<b>" . ($count - $j) . "</b> n'ont pas pu être supprimé, il se peut qu'elle soit utilisée");
                return $this->json(json_encode(["item" => null]));
            }
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

    private function editAndDeleteSecurity($user = null, $gerant = null, $proprietaire = null, $vendeur = null)
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
