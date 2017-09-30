<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Conseiller controller.
 *
 */
class ConseillerController extends Controller
{
    /**
     * Liste tous les conseillers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $this->listAndShowSecurity();
        $em = $this->getDoctrine()->getManager();
        $conseillers = $em->getRepository('APMMarketingDistribueBundle:conseiller')->findAll();
        return $this->render('APMMarketingDistribueBundle:conseiller:index.html.twig', array(
            'conseillers' => $conseillers,
        ));
    }

    private function listAndShowSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Creates a new Conseiller entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Session $session */
        $session = $request->getSession();
        /** @var Conseiller $conseiller */
        $conseiller = TradeFactory::getTradeProvider("conseiller");
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\ConseillerType', $conseiller);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $conseiller->setUtilisateur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseiller);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                $json = array();
                $json['item'] = array();
                $session->getFlashBag()->add('success', "<strong> conseiller. réf:" . $conseiller->getCode() . "</strong><br> Opération effectuée avec succès!");
                return $this->json(json_encode($json), 200);
            }
            return $this->redirectToRoute('apm_marketing_conseiller_show', array('id' => $conseiller->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:conseiller:new.html.twig', array(
            'conseiller' => $conseiller,
            'form' => $form->createView(),
        ));
    }

    private function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$user->isConseillerA1()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Conseiller entity.
     * @param Request $request
     * @param Conseiller $conseiller
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Conseiller $conseiller)
    {
        $this->listAndShowSecurity();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $conseiller->getId(),
                'code' => $conseiller->getCode(),
                'dateEnregistrement' => $conseiller->getDateEnregistrement()->format('d-m-Y H:i'),
                'dateCreationReseau' => $conseiller->getDateCreationReseau()->format('d-m-Y H:i'),
                'description' => $conseiller->getDescription(),
                'isConseillerA2' => $conseiller->getIsConseillerA2(),
                'nombreInstanceReseau' => $conseiller->getNombreInstanceReseau(),
                'matricule' => $conseiller->getMatricule(),
                'valeurQuota' => $conseiller->getValeurQuota(),
                'utilisateur' => $conseiller->getUtilisateur()->getUsername(),
                'masterConseiller' => $conseiller->getMasterConseiller()->getMatricule(),
                'conseillerDroite' => $conseiller->getConseillerDroite()->getMatricule(),
                'conseillerGauche' => $conseiller->getConseillerGauche()->getMatricule(),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($conseiller);
        $reseau_form = $this->createNewForm();
        return $this->render('APMMarketingDistribueBundle:conseiller:show.html.twig', array(
            'conseiller' => $conseiller,
            'delete_form' => $deleteForm->createView(),
            'reseau_form' => $reseau_form->createView(),
        ));
    }

    private function createNewForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_reseau_new'))
            ->setMethod('PUT')
            ->getForm();
    }


    /**
     * Creates a form to delete a Conseiller entity.
     *
     * @param Conseiller $conseiller The Conseiller entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Conseiller $conseiller)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_conseiller_delete', array('id' => $conseiller->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Conseiller entity.
     * @param Request $request
     * @param Conseiller $conseiller
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function editAction(Request $request, Conseiller $conseiller)
    {
        $this->editAndDeleteSecurity($conseiller);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\ConseillerType', $conseiller);
        $editForm->handleRequest($request);
        /** @var Session $session */
        $session = $request->getSession();
        if ($editForm->isSubmitted() && $editForm->isValid()
            || $request->isXmlHttpRequest() && $request->isMethod('POST')
        ) {
            $em = $this->getDoctrine()->getManager();
            try {
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json['item'] = array();
                    $property = $request->request->get('name');
                    $value = $request->request->get('value');
                    switch ($property) {
                        case 'isConseillerA2':
                            $conseiller->setIsConseillerA2($value);
                            break;
                        case 'description':
                            $conseiller->setDescription($value);
                            break;
                        case 'nombreInstanceReseau':
                            $conseiller->setNombreInstanceReseau($value);
                            break;
                        case 'matricule':
                            $conseiller->setMatricule($value);
                            break;
                        case 'valeurQuota':
                            $conseiller->setValeurQuota($value);
                            break;
                        case 'masterConseiller':
                            /** @var Conseiller $conseiller */
                            $conseiller = $em->getRepository('APMMarketingDistribueBundle:Conseiller')->find($value);
                            $conseiller->setMasterConseiller($conseiller);
                            break;
                        case 'conseillerDroite':
                            /** @var Conseiller $conseiller */
                            $conseiller = $em->getRepository('APMMarketingDistribueBundle:Conseiller')->find($value);
                            $conseiller->setConseillerDroite($conseiller);
                            break;
                        case 'conseillerGauche':
                            /** @var Conseiller $conseiller */
                            $conseiller = $em->getRepository('APMMarketingDistribueBundle:Conseiller')->find($value);
                            $conseiller->setConseillerGauche($conseiller);
                            break;
                        default:
                            $session->getFlashBag()->add('info', "<strong> Aucune mise à jour effectuée</strong>");
                            return $this->json(json_encode(["item" => null]), 205);
                    }
                    $em->flush();
                    $session->getFlashBag()->add('success', "Mise à jour du profile conseiller : <strong>" . $property . "</strong> réf. Conseiller :" . $conseiller->getMatricule() . "<br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                $em->flush();
                return $this->redirectToRoute('apm_marketing_conseiller_show', array('id' => $conseiller->getId()));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        $deleteForm = $this->createDeleteForm($conseiller);
        return $this->render('APMMarketingDistribueBundle:conseiller:edit.html.twig', array(
            'conseiller' => $conseiller,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Conseiller $conseiller
     */
    private function editAndDeleteSecurity($conseiller)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) ||
            ($conseiller->getUtilisateur() !== $user)
        ) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Deletes a Conseiller entity.
     * @param Request $request
     * @param Conseiller $conseiller
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     */
    public function deleteAction(Request $request, Conseiller $conseiller)
    {
        $this->editAndDeleteSecurity($conseiller);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $em->remove($conseiller);
            $em->flush();
            $json = array();
            $json['item'] = array();
            return $this->json(json_encode($json), 200);
        }
        $form = $this->createDeleteForm($conseiller);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($conseiller);
            $em = $this->getDoctrine()->getManager();
            $em->remove($conseiller);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_conseiller_index');
    }

    public function deleteFromListAction(Conseiller $conseiller)
    {
        $this->editAndDeleteSecurity($conseiller);
        $em = $this->getDoctrine()->getManager();
        $em->remove($conseiller);
        $em->flush();

        return $this->redirectToRoute('apm_marketing_conseiller_index');
    }
}
