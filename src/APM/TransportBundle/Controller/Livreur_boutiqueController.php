<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Livreur_boutique;
use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Factory\TradeFactory;
use APM\VenteBundle\Entity\Boutique;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Livreur_boutique controller.
 * @RouteResource("livreur", pluralize=false)
 */
class Livreur_boutiqueController extends Controller
{
    private $reference_filter;
    private $transporteur_filter;
    private $boutique_filter;

    /**
     * Tout le monde peut Lister les livreurs d'une boutique
     * un livreur peut appartenir à plusieurs boutiques
     *
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Get("/cget/livreurs/boutique/{id}", name="s_boutique")
     */
    public function getAction(Boutique $boutique)
    {
        $this->listeAndShowSecurity();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['items'] = array();
            $q = $request->query->get('q');
            $this->reference_filter = $request->request->has('reference_filter') ? $request->request->get('reference_filter') : "";
            $this->transporteur_filter = $request->request->has('transporteur_filter') ? $request->request->get('transporteur_filter') : "";
            $this->boutique_filter = $request->request->has('boutique_filter') ? $request->request->get('boutique_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            if ($q === "guest" || $q === "all") {
                $livreurs = $boutique->getLivreurs();//livreurs étrangers: empruntés
                if (null !== $livreurs) {
                    $iTotalRecords = count($livreurs);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $livreurs = $this->handleResults($livreurs, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    /** @var Livreur_boutique $livreur */
                    foreach ($livreurs as $livreur) {
                        array_push($json['items'], array(
                            'id' => $livreur->getId(),
                            'reference' => $livreur->getReference(),
                        ));
                    }
                }
            }
            if ($q === "owner" || $q === "all") {
                $livreurs = $boutique->getLivreurBoutiques();
                if (null !== $livreurs) {
                    $iTotalRecords = count($livreurs);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $livreurs = $this->handleResults($livreurs, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    foreach ($livreurs as $livreur) {
                        array_push($json['items'], array(
                            'id' => $livreur->getId(),
                            'reference' => $livreur->getReference(),
                        ));
                    }
                }
            }

            return $this->json(json_encode($json), 200);
        }

        $livreurs_Empruntes = $boutique->getLivreurs();//livreurs étrangers: empruntés
        $livreurs_boutiques = $boutique->getLivreurBoutiques();//livreurs crées par la boutique

        return $this->render('APMTransportBundle:livreur_boutique:index.html.twig', array(
            'livreurs_boutiques' => $livreurs_boutiques,
            'livreurs_Empruntes' => $livreurs_Empruntes,
            'boutique' => $boutique,
        ));
    }

    private function listeAndShowSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have the role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $livreurs
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($livreurs, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($livreurs === null) return array();

        if ($this->reference_filter != null) {
            $livreurs = $livreurs->filter(function ($e) {//filtrage select
                /** @var Livreur_boutique $e */
                return $e->getReference() === $this->reference_filter;
            });
        }
        if ($this->transporteur_filter != null) {
            $livreurs = $livreurs->filter(function ($e) {//filtrage select
                /** @var Livreur_boutique $e */
                return $e->getTransporteur()->getMatricule() ===  $this->transporteur_filter;
            });
        }

        if ($this->boutique_filter != null) {
            $livreurs = $livreurs->filter(function ($e) {//search for occurences in the text
                /** @var Livreur_boutique $e */
                return $e->getBoutiqueProprietaire()->getCode() === $this->boutique_filter;
            });
        }

        $livreurs = ($livreurs !== null) ? $livreurs->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $livreurs, function ($e1, $e2) {
            /**
             * @var Livreur_boutique $e1
             * @var Livreur_boutique $e2
             */
            $dt1 = $e1->getDateEnregistrement()->getTimestamp();
            $dt2 = $e2->getDateEnregistrement()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $livreurs = array_slice($livreurs, $iDisplayStart, $iDisplayLength, true);

        return $livreurs;
    }

    // les boutiques sont responsables de la création des livreur_boutiques

    /**
     * @ParamConverter("transporteur", options={"mapping":{"transporteur_id":"id"}})
     * @param Request $request
     * @param Boutique $boutique
     * @param Profile_transporteur $transporteur
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Post("/new/livreur/boutique/{id}/transporteur/{transporteur_id}")
     */
    public function newAction(Request $request, Boutique $boutique, Profile_transporteur $transporteur)
    {
        $this->createSecurity($boutique);
        /** @var Livreur_boutique $livreur_boutique */
        $livreur_boutique = TradeFactory::getTradeProvider("livreur_boutique");
        $form = $this->createForm('APM\TransportBundle\Form\Livreur_boutiqueType', $livreur_boutique);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Profile_transporteur $transporteur */
            $livreur_boutique->setTransporteur($transporteur);
            $livreur_boutique->setBoutiqueProprietaire($boutique);
            $transporteur->setLivreurBoutique($livreur_boutique);
            $em = $this->getDoctrine()->getManager();
            $em->persist($livreur_boutique);
            $em->flush();
            if($request->isXmlHttpRequest()){
                $json = array();
                $json['item'] =array();
                return $this->json(json_encode($json),200);
            }
            return $this->redirectToRoute('apm_transport_livreur_boutique_show', array('id' => $livreur_boutique->getId()));
        }

        return $this->render('APMTransportBundle:livreur_boutique:new.html.twig', array(
            'livreur_boutique' => $livreur_boutique,
            'boutique' => $boutique,
            'form' => $form->createView(),
        ));
    }

    /**
     * uniquement les gerants et proprietaires de boutique sont autorisées à créer des livreurs boutique
     * @param Boutique $boutique
     */
    private function createSecurity($boutique)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $gerant = $boutique->getGerant();
        $proprietaire = $boutique->getProprietaire();
        if ($user !== $gerant && $user !== $proprietaire) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Tout le monde peut voir les détail d'un livreur boutique
     * @param Request $request
     * @param Livreur_boutique $livreur_boutique
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Get("/show/livreur/{id}")
     */
    public function showAction(Request $request, Livreur_boutique $livreur_boutique)
    {
        $this->listeAndShowSecurity();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $livreur_boutique->getId(),
                'reference' => $livreur_boutique->getReference(),
                'transporteur' => $livreur_boutique->getTransporteur()->getId(),
                'boutiqueProprietaire' => $livreur_boutique->getBoutiqueProprietaire()->getId(),
                'dateEnregistrement' => $livreur_boutique->getDateEnregistrement()->format('d-m-Y H:i'),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($livreur_boutique);
        return $this->render('APMTransportBundle:livreur_boutique:show.html.twig', array(
            'livreur_boutique' => $livreur_boutique,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Livreur_boutique entity.
     *
     * @param Livreur_boutique $livreur_boutique The Livreur_boutique entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private
    function createDeleteForm(Livreur_boutique $livreur_boutique)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_transport_livreur_boutique_delete', array('id' => $livreur_boutique->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Livreur_boutique entity.
     * @param Request $request
     * @param Livreur_boutique $livreur_boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|JsonResponse
     *
     * @Post("/edit/livreur/{id}")
     */
    public function editAction(Request $request, Livreur_boutique $livreur_boutique)
    {
        $this->editAndDeleteSecurity($livreur_boutique);
        $deleteForm = $this->createDeleteForm($livreur_boutique);
        $editForm = $this->createForm('APM\TransportBundle\Form\Livreur_boutiqueType', $livreur_boutique);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()
            || $request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            if($request->isXmlHttpRequest()){
                try {
                        if ($request->isXmlHttpRequest()) {
                        $json = array();
                        $json['item'] = array();
                        $property = $request->request->get('name');
                        $value = $request->request->get('value');
                        switch ($property) {
                            case 'boutique':
                                /** @var Boutique $boutique */
                                $boutique = $em->getRepository('APMVenteBundle:Boutique')->find($value);
                                $livreur_boutique->addBoutique($boutique);
                                break;
                            default:
                                $session->getFlashBag()->add('info', "<strong> Aucune mise à jour effectuée</strong>");
                                return $this->json(json_encode(["item" => null]), 205);
                        }
                        $em->flush();
                        $session->getFlashBag()->add('success', "Ajout d'une <strong>" . $property . "</strong> réf. livreur :" . $livreur_boutique->getReference() . "<br> Opération effectuée avec succès!");
                        return $this->json(json_encode($json), 200);
                    }
                    $em->flush();
                    return $this->redirectToRoute('apm_transport_transporteur_show', array('id' => $livreur_boutique->getId()));
                } catch (ConstraintViolationException $cve) {
                    $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                    return $this->json(json_encode(["item" => null]));
                } catch (AccessDeniedException $ads) {
                    $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                    return $this->json(json_encode(["item" => null]));
                }
            }
        }

        return $this->render('APMTransportBundle:livreur_boutique:edit.html.twig', array(
            'livreur_boutique' => $livreur_boutique,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Livreur_boutique $livreur_boutique
     */
    private function editAndDeleteSecurity($livreur_boutique)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $boutique = $livreur_boutique->getBoutiqueProprietaire();
        $gerant = $boutique->getGerant();
        $proprietaire = $boutique->getProprietaire();
        if ($user !== $gerant && $user !== $proprietaire) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Deletes a Livreur_boutique entity.
     * @param Request $request
     * @param Livreur_boutique $livreur_boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     *
     * @Delete("/delete/livreur/{id}")
     */
    public function deleteAction(Request $request, Livreur_boutique $livreur_boutique)
    {
        $this->editAndDeleteSecurity($livreur_boutique);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array();
            $em->remove($profile_transporteur);
            $em->flush();
            return $this->json(json_encode($json), 200);
        }
        $form = $this->createDeleteForm($livreur_boutique);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($livreur_boutique);
            $em->flush();
        }

        return $this->redirectToRoute('apm_transport_livreur_boutique_index', ['id' => $livreur_boutique->getBoutiqueProprietaire()->getId()]);
    }

}
