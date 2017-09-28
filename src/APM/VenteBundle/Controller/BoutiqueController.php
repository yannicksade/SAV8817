<?php

namespace APM\VenteBundle\Controller;

use APM\UserBundle\Entity\Admin;
use APM\UserBundle\Entity\Utilisateur;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Boutique controller.
 *
 */
class BoutiqueController extends Controller
{
    private $designation_filter;
    private $code_filter;
    private $etat_filter;
    private $nationalite_filter;
    #private $dateTo_filter;
    #private $dateFrom_filter;

    /**
     * récupérer ou lister toutes les boutiques appartenant ou gérées par l'utilisateur courrant ou fourni en argument
     * @param Request $request
     * @param Utilisateur_avm|null $user
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, Utilisateur_avm $user = null)
    {
        $this->personalSecurity();
        /** @var Utilisateur_avm $user */
        if (null === $user) {
            $user = $this->getUser();
        } else {
            $this->adminSecurity();
        }
        //filtre
        /** @var Boutique $boutique */
        $boutiques = $user->getBoutiquesProprietaire();
        $boutiquesGerant = $user->getBoutiquesGerant();
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $p = $request->get('p');
            $this->nationalite_filter = $request->request->has('nationalite_filter') ? $request->request->get('nationalite_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->designation_filter = $request->request->has('designation_filter') ? $request->request->get('designation_filter') : "";
            $this->etat_filter = $request->request->has('etat_filter') ? $request->request->get('etat_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;

            $json = array();
            if (($p === "owner" || $p === "both") && null !== $boutiques) {
                $iTotalRecords = count($boutiques);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $boutiques = $this->handleResults($boutiques, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                //filtre
                foreach ($boutiques as $boutique) {
                    array_push($json, array(
                        'value' => $boutique->getId(),
                        'text' => $boutique->getDesignation(),
                    ));
                }
            }
            if (($p === "shopkeeper" || $p === "both") && null !== $boutiquesGerant) {
                $iTotalRecords = count($boutiquesGerant);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $boutiquesGerant = $this->handleResults($boutiquesGerant, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                foreach ($boutiquesGerant as $boutique) {
                    array_push($json, array(
                        'value' => $boutique->getId(),
                        'text' => $boutique->getDesignation(),
                    ));
                }
            }
            return $this->json($json, 200);
        }

        return $this->render('APMVenteBundle:boutique:index.html.twig', array(
            'boutiquesProprietaire' => $boutiques,
            'boutiquesGerant' => $boutiquesGerant,
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    /**
     * @param Collection $boutiques
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($boutiques, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($boutiques === null) return array();

        if ($this->code_filter != null) {
            $boutiques = $boutiques->filter(function ($e) {//filtrage select
                /** @var Boutique $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->etat_filter != null) {
            $boutiques = $boutiques->filter(function ($e) {//filtrage select
                /** @var Boutique $e */
                return $e->getEtat() === intval($this->etat_filter);
            });
        }
        if ($this->nationalite_filter != null) {
            $boutiques = $boutiques->filter(function ($e) {//filter with the begining of the entering word
                /** @var Boutique $e */
                $str1 = $e->getNationalite();
                $str2 = $this->nationalite_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->designation_filter != null) {
            $boutiques = $boutiques->filter(function ($e) {//search for occurences in the text
                /** @var Boutique $e */
                $subject = $e->getDesignation();
                $pattern = $this->designation_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $boutiques = ($boutiques !== null) ? $boutiques->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $boutiques, function ($e1, $e2) {
            /**
             * @var Boutique $e1
             * @var Boutique $e2
             */
            $dt1 = $e1->getUpdatedAt()->getTimestamp();
            $dt2 = $e2->getUpdatedAt()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $boutiques = array_slice($boutiques, $iDisplayStart, $iDisplayLength, true);

        return $boutiques;
    }

    /**
     * Creates a new Boutique entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Session $session */
        $session = $request->getSession();
        /** @var Boutique $boutique */
        $boutique = TradeFactory::getTradeProvider('boutique');
        $form = $this->createForm('APM\VenteBundle\Form\BoutiqueType', $boutique);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $boutique->setProprietaire($this->getUser());
                $em = $this->getEM();
                $em->persist($boutique);
                $em->flush();
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json["item"] = array(//prevenir le client
                        "action" => 0,
                        "id" => null,
                    );
                    $session->getFlashBag()->add('success', "<strong> Boutique créée. réf:" . $boutique->getCode() . "</strong><br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json));
                }
                $this->get('apm_core.crop_image')->liipImageResolver($boutique->getImage());
                //$dist = dirname(__DIR__, 4);
                //$file = $dist . '/web/' . $this->getParameter('images_url') . '/' . $boutique->getImage();
                if (null !== $boutique->getImage()) {
                    return $this->redirectToRoute('apm_vente_boutique_show-image', array('id' => $boutique->getId()));
                } else {
                    return $this->redirectToRoute('apm_vente_boutique_show', array('id' => $boutique->getId()));
                }
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
        return $this->render('APMVenteBundle:boutique:new.html.twig', array(
            'form' => $form->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    private
    function getEM()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    public
    function showImageAction(Request $request, Boutique $boutique)
    {
        $this->listAndShowSecurity();
        $form = $this->createCrobForm($boutique);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('apm_core.crop_image')->setCropParameters(intval($_POST['x']), intval($_POST['y']), intval($_POST['w']), intval($_POST['h']), $boutique->getImage(), $boutique);

            return $this->redirectToRoute('apm_vente_boutique_show', array('id' => $boutique->getId()));
        }

        return $this->render('APMVenteBundle:boutique:image.html.twig', array(
            'boutique' => $boutique,
            'crop_form' => $form->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    private
    function createCrobForm(Boutique $boutique)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_boutique_show-image', array('id' => $boutique->getId())))
            ->setMethod('POST')
            ->getForm();
    }

    /**
     * Finds and displays a Boutique entity.
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public
    function showAction(Request $request, Boutique $boutique)
    {
        $this->listAndShowSecurity();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $boutique->getId(),
                'designation' => $boutique->getDesignation(),
                'nationalite' => $boutique->getNationalite(),
                'description' => $boutique->getDescription(),
                'publiable' => $boutique->getPubliable(),
                'raisonSociale' => $boutique->getRaisonSociale(),
                'statutSocial' => $boutique->getStatutSocial(),
                'gerant' => $boutique->getGerant()->getUsername(),
                'proprietaire' => $boutique->getProprietaire(),
                'etat' => $boutique->getEtat(),
            );
            return $this->json(json_encode($json), 200);
        }

        $deleteForm = $this->createDeleteForm($boutique);
        return $this->render('APMVenteBundle:boutique:show.html.twig', array(
            'boutique' => $boutique,
            'delete_form' => $deleteForm->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    /**
     * Creates a form to delete a Boutique entity.
     *
     * @param Boutique $boutique The Boutique entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private
    function createDeleteForm(Boutique $boutique)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_boutique_delete', array('id' => $boutique->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Boutique entity.
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public
    function editAction(Request $request, Boutique $boutique)
    {
        $this->editAndDeleteSecurity($boutique);
        $oldGerant = $boutique->getGerant();
        $em = $this->getEM();
        /** @var Session $session */
        $session = $request->getSession();
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $json = array();
            $json['item'] = array();
            $property = $request->request->get('name');
            $value = $request->request->get('value');
            switch ($property) {
                case 'etat':
                    $boutique->setEtat($value);
                    $json["item"] = array(//pour actualiser la table
                        "action" => 1,
                    );
                    break;
                case 'publiable':
                    $boutique->setPubliable($value);
                    break;
                case 'nationalite':
                    $boutique->setNationalite($value);
                    break;
                case 'raisonSociale' :
                    $boutique->setRaisonSociale($value);
                    break;
                case 'statuSociale' :
                    $boutique->setStatutSocial($value);
                    break;
                case 'description':
                    $boutique->setDescription($value);
                    break;
                case 'designation':
                    $boutique->setDesignation($value);
                    $json["item"] = array(//pour actualiser la table
                        "action" => 1,
                    );
                    break;
                case 'gerant':
                    $newGerant = $em->getRepository('APMUserBundle:Utilisateur_avm')->find($value);
                    $this->personnelBoutique($boutique, $oldGerant, $newGerant);
                    $boutique->setGerant($newGerant);
                    break;
                default:
                    $session->getFlashBag()->add('info', "<strong> Aucune mis à jour effectuée</strong>");
                    return $this->json(json_encode(["item" => null]), 205);
            }
            $em->flush();
            $session->getFlashBag()->add('success', "Mis à jour propriété : <strong>" . $property . "</strong> réf. Boutique :" . $boutique->getCode() . "<br> Opération effectuée avec succès!");
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($boutique);
        $editForm = $this->createForm('APM\VenteBundle\Form\BoutiqueType', $boutique);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($boutique);
            //si le proprietaire change de gerant, il est remplacé dans touts les offres de la boutique
            $this->personnelBoutique($boutique, $oldGerant, $editForm->get('gerant')->getData());
            $em->persist($boutique);
            $em->flush();
            //---
            $dist = dirname(__DIR__, 4);
            $file = $dist . '/web/' . $this->getParameter('images_url') . '/' . $boutique->getImage();
            if (file_exists($file)) {
                return $this->redirectToRoute('apm_vente_boutique_show-image', array('id' => $boutique->getId()));
            } else {
                return $this->redirectToRoute('apm_vente_boutique_show', array('id' => $boutique->getId()));
            }
            //---
        }

        return $this->render('APMVenteBundle:boutique:edit.html.twig', array(
            'boutique' => $boutique,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    /**
     * @param Boutique $boutique
     */
    private
    function editAndDeleteSecurity($boutique)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');

        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || !$user instanceof Utilisateur || ($boutique->getProprietaire() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /** Cette fonction remplace le vendeur des offres d'une boutique par un nouvel utilisateur
     * @param Boutique $boutique
     * @param $oldGerant
     * @param $newGerant
     */
    private
    function personnelBoutique($boutique, $oldGerant, $newGerant)
    {
        if ($newGerant !== $oldGerant) {
            /** @var Offre $offre */
            foreach ($boutique->getOffres() as $offre) {
                if ($offre->getVendeur() === $oldGerant) {
                    $offre->setVendeur($newGerant);
                }
            }
        }
    }

    /**
     * Deletes a Boutique entity.
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public
    function deleteAction(Request $request, Boutique $boutique)
    {
        $this->editAndDeleteSecurity($boutique);
        $em = $this->getEM();

        if ($request->isXmlHttpRequest()) {
            $em->remove($boutique);
            $em->flush();
            $json = array();
            $json['item'];
            return $this->json(json_encode($json), 200);
        }

        $form = $this->createDeleteForm($boutique);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($boutique);
            $em->flush();
        }
        return $this->redirectToRoute('apm_vente_boutique_index');
    }

    /*changer le personnel ayant le droit sur les produits de la
     * changer les droits sur les offres
    */
    public
    function deleteFromListAction(Boutique $boutique)
    {
        $this->editAndDeleteSecurity($boutique);
        $em = $this->getDoctrine()->getManager();
        $em->remove($boutique);
        $em->flush();

        return $this->redirectToRoute('apm_vente_boutique_index');
    }


    private
    function personalSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') || !$this->getUser() instanceof Utilisateur) {
            throw $this->createAccessDeniedException();
        }

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

    private
    function listAndShowSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') || !$this->getUser() instanceof Utilisateur) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    private
    function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        /* ensure that the user is logged in
        */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$this->getUser() instanceof Utilisateur) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }
}
