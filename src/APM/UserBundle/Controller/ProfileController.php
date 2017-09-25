<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 06/06/2017
 * Time: 10:10
 */

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Admin;
use APM\UserBundle\Entity\Utilisateur;
use APM\UserBundle\Entity\Utilisateur_avm;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use PUGX\MultiUserBundle\Form\FormFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends Controller
{
    /**
     * Show the user.
     * @param Request $request
     * @param Utilisateur $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Utilisateur $user = null)
    {
        if(null === $user) $user = $this->getUser();
            if (!is_object($user) || !$user instanceof UserInterface) {
                throw new AccessDeniedException('This user does not have access to this section.');
            }
            if ($request->isXmlHttpRequest()) {
                $json = array();
                $json['item'] = array(
                    'id' => $user->getId(),
                    'code' => $user->getCode(),
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'email' => $user->getEmail(),
                    'username' => $user->getUsername(),
                    'enabled' => $user->isEnabled(),
                    'profession' => $user->getProfession(),
                    'dateNaissance' => $user->getDateNaissance(),
                    'pays' => $user->getPays(),
                    'genre' => $user->getGenre(),
                    'telephone' => $user->getTelephone(),
                    'adresse' => $user->getAdresse(),
                    'etatDuCompte' => $user->getEtatDuCompte(),
                    'image' => $user->getImage(),
                    'dateEnregistrement' => $user->getDateEnregistrement()->format('d-m-Y H:i'),
                    'updatedAt' => $user->getUpdatedAt()->format('d-m-Y H:i'),
                );
                return $this->json(json_encode($json), 200);
            }
            return $this->render('@FOSUser/Profile/show.html.twig', array(
                'user' => $user,
                'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
                'type' => $user instanceof Admin,
            ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $json = array();
            $json['item'] = array();
            /** @var Session $session */
            $session = $request->getSession();
            $em = $this->getDoctrine()->getManager();
            $property = $request->request->get('name');
            $value = $request->request->get('value');
            switch ($property) {
                case 'etatDuCompte' :
                    $user->setEtatDuCompte($value);
                    break;
                case 'username':
                    $user->setUsername($value);
                    break;
                case 'email' :
                    $user->setEmail($value);
                    break;
                case 'enabled' :
                    $user->setEnabled($value);
                    break;
                case 'nom' :
                    $user->setNom($value);
                    break;
                case 'prenom' :
                    $user->setPrenom($value);
                    break;
                case 'profession' :
                    $user->setProfession($value);
                    break;
                case 'dateNaissance' :
                    $user->setDateNaissance($value);
                    break;
                case 'pays' :
                    $user->setPays($value);
                    break;
                case 'genre' :
                    $user->setGenre($value);
                    break;
                case 'telephone' :
                    $user->setTelephone($value);
                    break;
                case 'adresse' :
                    $user->setAdresse($value);
                    break;
                case 'image' :
                    $user->setImageFile($value);
                    break;
                default:
                    $session->getFlashBag()->add('info', "<strong> Aucune mis à jour effectuée</strong>");
                    return $this->json(json_encode(["item" => null]), 205);
            }
            $em->flush();
            $session->getFlashBag()->add('success', "Mis à jour profile. Propriété <strong>" . $property . "</strong> mis à jour<br> Opération effectuée avec succès!");
            return $this->json(json_encode($json), 200);
        }
        /** @var FormFactory $formFactory */
        $formFactory = $this->get('fos_user.profile.form.factory');
        $form = $formFactory->createForm();
        $form->setData($user);
        $event = new FormEvent($form, $request);
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);
            $this->get('apm_core.crop_image')->liipImageResolver($user->getImage());
            //---
            $dist = dirname(__DIR__, 4);
            $file = $dist . '/web/' . $this->getParameter('images_url') . '/' . $user->getImage();
            if (file_exists($file)) {
                $url = $this->generateUrl('apm_user_profile_show-image', array('id' => $user->getId()));
            } else {
                $url = $this->generateUrl('fos_user_profile_show');
            }
            //---
            $response = new RedirectResponse($url);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
            return $response;
        }

        return $this->render('@FOSUser/Profile/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function showImageAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->createCrobForm($user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('apm_core.crop_image')->setCropParameters(intval($_POST['x']), intval($_POST['y']), intval($_POST['w']), intval($_POST['h']), $user->getImage(), $user);
            $event = new FormEvent($form, $request);
            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            return $response;
        }

        return $this->render('APMUserBundle:utilisateur_avm:image.html.twig', array(
            'user' => $user,
            'crop_form' => $form->createView(),
        ));
    }

    private function createCrobForm(Utilisateur $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_user_profile_show-image', array('id' => $user->getId())))
            ->setMethod('POST')
            ->getForm();
    }
}