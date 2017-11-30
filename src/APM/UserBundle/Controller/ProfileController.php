<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 28/11/2017
 * Time: 09:17
 */

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Admin;
use APM\UserBundle\Entity\Utilisateur_avm;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use APM\UserBundle\Entity\Utilisateur;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends FOSRestController
{
    /**
     * Show the user.
     * @param Utilisateur $user
     * @return JsonResponse
     * @Get("/show/profile/{id}")
     */
    public function showAction(Utilisateur $user)
    {
        $serializerContext = SerializationContext::create()->enableMaxDepthChecks();
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($user, 'json', $serializerContext->setGroups(array("list")));
        // 'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img')
        return $this->json($data, 200);
    }

    /**
     * @param Request $request
     * @param Utilisateur_avm $user
     * @return View|JsonResponse|Response
     *
     * @Patch("/patch/profile/user/{id}")
     */
    public function patchUserAction(Request $request, Utilisateur_avm $user)
    {
        $this->securityUser($user);
        /** @var Utilisateur_avm $utilisateur */
        $response = $this->get('apm_user.update_profile_manager')->updateUser(Utilisateur_avm::class, $request, false, $user);
        if (is_object($response) && $response instanceof Utilisateur_avm) {
            $utilisateur = $response;
            return $this->routeRedirectView('api_user_show', ['id' => $utilisateur->getId()], Response::HTTP_NO_CONTENT);
        }

        return $response;
    }

    private function securityUser($user)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', $user, 'This user does not have access to this section.');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //-----------------------------------------------------------------------------------------
    }

    /**
     * @param Request $request
     * @param Admin $user
     * @return View|JsonResponse
     *
     * @Patch("/patch/profile/staff/{id}")
     */
    public function patchStaffAction(Request $request, Admin $user)
    {
        $this->securityStaff($user);
        $response = $this->get('apm_user.update_profile_manager')->updateUser(Admin::class, $request, false, $user);
        if (is_object($response) && $response instanceof Admin) {
            $utilisateur = $response;
            return $this->routeRedirectView('api_user_show', ['id' => $utilisateur->getId()], Response::HTTP_NO_CONTENT);
        }
        return $response;
    }

    private function securityStaff($user)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_STAFF', $user, 'This user does not have access to this page.');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //-----------------------------------------------------------------------------------------
    }

    /**
     * Change user password
     *
     * @Post("/change-password/user/{id}")
     * @param Request $request
     * @param Utilisateur_avm $user
     * @return FormInterface|JsonResponse
     */
    public function changepasswordUserAction(Request $request, Utilisateur_avm $user)
    {
        $this->securityUser($user);
        if ($user !== $this->getUser()) {
            throw new AccessDeniedHttpException("This user does not have access to this section");
        }
        return $this->get('apm_user.resetting_manager')->change(Utilisateur_avm::class, $request, $user);
    }

    /**
     * Change user password
     *
     * @Post("/change-password/staff/{id}")
     * @param Request $request
     * @param Admin $user
     * @return FormInterface|JsonResponse
     */
    public function changepasswordStaffAction(Request $request, Admin $user)
    {
        $this->securityStaff($user);
        if ($user !== $this->getUser()) {
            throw new AccessDeniedHttpException("This user does not have access to this section");
        }
        return $this->get('apm_user.resetting_manager')->change(Admin::class, $request, $user);
    }

    /*public function showImageAction(Request $request)
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
     }*/

}