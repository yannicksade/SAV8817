<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Livreur_boutique;
use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Profile_transporteur controller.
 * @RouteResource("transporteur", pluralize=false)
 */
class Profile_transporteurController extends Controller
{
    private $matricule_filter;
    private $code_filter;
    private $livreur_boutique;
    private $isLivreur_boutique_filter;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Get("/transporteurs", name="s")
     */
    public function getAction(Request $request)
    {
        $this->listeAndShowSecurity();
        $em = $this->getDoctrine()->getManager();
        $transporteurs = $em->getRepository('APMTransportBundle:Profile_transporteur')->findAll();
        $profile_transporteurs =  new ArrayCollection($transporteurs);
        if($request->isXmlHttpRequest()){
            $json =array();
            $json['items'] = array();
            $this->matricule_filter = $request->request->has('matricule_filter') ? $request->request->get('matricule_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->livreur_boutique = $request->request->has('livreur_boutique') ? $request->request->get('livreur_boutique') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $iTotalRecords = count($profile_transporteurs);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $profile_transporteurs = $this->handleResults($profile_transporteurs, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            //filtre
            /** @var Profile_transporteur $transporteur */
            foreach ($profile_transporteurs as $transporteur) {
                array_push($json['items'], array(
                    'id' => $transporteur->getId(),
                    'code' => $transporteur->getCode(),
                    'matricule' => $transporteur->getMatricule(),
                    'description' => $transporteur->getDescription(),

                ));
            }
            return $this->json(json_encode($json),200);
        }
        return $this->render('APMTransportBundle:profile_transporteur:index.html.twig', array(
            'profile_transporteurs' => $profile_transporteurs,
            'zone' => null,
        ));
    }

    private function listeAndShowSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $transporteurs
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($transporteurs, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($transporteurs === null) return array();

        if ($this->code_filter != null) {
            $transporteurs = $transporteurs->filter(function ($e) {//filtrage select
                /** @var Profile_transporteur $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->matricule_filter != null) {
            $transporteurs = $transporteurs->filter(function ($e) {//filtrage select
                /** @var Profile_transporteur $e */
                return $e->getMatricule() ===  $this->matricule_filter;
            });
        }

        if ($this->isLivreur_boutique_filter != null) {
            $transporteurs = $transporteurs->filter(function ($e) {//search for occurences in the text
                /** @var Profile_transporteur $e */
                return $e->getLivreurBoutique() instanceof Livreur_boutique;
            });
        }

        $transporteurs = ($transporteurs !== null) ? $transporteurs->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $transporteurs, function ($e1, $e2) {
            /**
             * @var Profile_transporteur $e1
             * @var Profile_transporteur $e2
             */
            $dt1 = $e1->getDateEnregistrement()->getTimestamp();
            $dt2 = $e2->getDateEnregistrement()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $transporteurs = array_slice($transporteurs, $iDisplayStart, $iDisplayLength, true);

        return $transporteurs;
    }

    /**
     * Creates a new Profile_transporteur entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response| JsonResponse
     *
     * @Post("/new/transporteur")
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Profile_transporteur $profile_transporteur */
        $profile_transporteur = TradeFactory::getTradeProvider("transporteur");
        $form = $this->createForm('APM\TransportBundle\Form\Profile_transporteurType', $profile_transporteur);
        $form->remove('utilisateur');
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $profile_transporteur->setUtilisateur($this->getUser());
                $em->persist($profile_transporteur);
                $em->flush();
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json['item'] = array();
                    return $this->json(json_encode($json), 200);
                }
                return $this->redirectToRoute('apm_transport_transporteur_show', array('id' => $profile_transporteur->getId()));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        return $this->render('APMTransportBundle:profile_transporteur:new.html.twig', array(
            'profile_transporteur' => $profile_transporteur,
            'form' => $form->createView(),
        ));
    }

    private function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_TRANSPORTEUR', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //vérifier si l'utilisateur n'est pas déjà enregistré comme transporteur ou livreur
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $utilisateur = null;
        $utilisateur = $em->getRepository('APMTransportBundle:Profile_transporteur')->findOneBy(['utilisateur' => $user->getId()]);
        if (null !== $utilisateur) throw $this->createAccessDeniedException();
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Profile_transporteur entity.
     * @param Request $request
     * @param Profile_transporteur $profile_transporteur
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Get("/show/transporteur/{id}")
     */
    public function showAction(Request $request, Profile_transporteur $profile_transporteur)
    {
        $this->listeAndShowSecurity();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $profile_transporteur->getId(),
                'matricule' => $profile_transporteur->getMatricule(),
                'code' => $profile_transporteur->getCode(),
                'description' => $profile_transporteur->getDescription(),
                'dateEnregistrement' => $profile_transporteur->getDateEnregistrement()->format('d-m-Y H:i'),
                'utilisateur' => $profile_transporteur->getUtilisateur()->getId(),
                'livreur' => $profile_transporteur->getLivreurBoutique()->getId(),

            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($profile_transporteur);
        return $this->render('APMTransportBundle:profile_transporteur:show.html.twig', array(
            'profile_transporteur' => $profile_transporteur,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Profile_transporteur entity.
     *
     * @param Profile_transporteur $profile_transporteur The Profile_transporteur entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Profile_transporteur $profile_transporteur)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_transport_transporteur_delete', array('id' => $profile_transporteur->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Profile_transporteur entity.
     * @param Request $request
     * @param Profile_transporteur $profile_transporteur
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Post("/edit/transporteur/{id}")
     */
    public function editAction(Request $request, Profile_transporteur $profile_transporteur)
    {
        $this->editAndDeleteSecurity($profile_transporteur);
        $deleteForm = $this->createDeleteForm($profile_transporteur);
        $editForm = $this->createForm('APM\TransportBundle\Form\Profile_transporteurType', $profile_transporteur);
        $editForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($editForm->isSubmitted() && $editForm->isValid() ||
            $request->isXmlHttpRequest() && $request->getMethod() === "POST"
        ) {
            try {
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json['item'] = array();
                    $property = $request->request->get('name');
                    $value = $request->request->get('value');
                    switch ($property) {
                        case 'matricule':
                            $profile_transporteur->setMatricule($value);
                            break;
                        case 'livreurBoutique':
                            $transporteur = $em->getRepository('APMTransportBundle:Livreur_boutique')->find($value);
                            $profile_transporteur->setLivreurBoutique($transporteur);
                            break;
                        default:
                            $session->getFlashBag()->add('info', "<strong> Aucune mise à jour effectuée</strong>");
                            return $this->json(json_encode(["item" => null]), 205);
                    }
                    $em->flush();
                    $session->getFlashBag()->add('success', "Mis à jour propriété : <strong>" . $property . "</strong> réf. profile_transporteur :" . $boutique->getCode() . "<br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                $em->flush();
                return $this->redirectToRoute('apm_transport_transporteur_show', array('id' => $profile_transporteur->getId()));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        return $this->render('APMTransportBundle:profile_transporteur:edit.html.twig', array(
            'profile_transporteur' => $profile_transporteur,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Profile_transporteur $transporteur
     */
    private function editAndDeleteSecurity($transporteur)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_TRANSPORTEUR', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //autoriser la modification uniquement qau transporteur autonome de droit exclut tout livreur boutique
        $user = $this->getUser();
        if ($transporteur->getLivreurBoutique() || $user !== $transporteur->getUtilisateur()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Deletes a Profile_transporteur entity.
     * @param Request $request
     * @param Profile_transporteur $profile_transporteur
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     *
     * @Delete("/delete/transporteur/{id}")
     */
    public function deleteAction(Request $request, Profile_transporteur $profile_transporteur)
    {
        $this->editAndDeleteSecurity($profile_transporteur);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $em->remove($profile_transporteur);
            $em->flush();
            $json = array();
            $json['item'] = array();
            return $this->json(json_encode($json), 200);
        }
        $form = $this->createDeleteForm($profile_transporteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($profile_transporteur);
            $em->flush();
            return $this->redirectToRoute('apm_transport_transporteur_index');
        }

        return $this->redirectToRoute('apm_transport_transporteur_index');
    }

}
