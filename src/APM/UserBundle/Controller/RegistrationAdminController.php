<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Admin;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class RegistrationAdminController
 */
class RegistrationAdminController extends FOSRestController
{
    /**
     * @Post("/register", name="_staff")
     * @param Request $request
     * @return JsonResponse|FormInterface|Response
     */
    public function registerAction(Request $request)
    {
        $this->security($this->getUser());
        /** @var Admin $user */
        $response = $this->get('apm_user.registration_manager')->register(Admin::class, $request);

        if (is_object($response) && $response instanceof Admin) {
            $user = $response;
            $response = new JsonResponse(
                [
                    'msg' => $this->get('translator')->trans('registration.flash.user_created', [], 'FOSUserBundle'),
                    'token' => $this->get('lexik_jwt_authentication.jwt_manager')->create($user)
                ],
                Response::HTTP_CREATED,
                [
                    'location' => $this->generateUrl(
                        'api_user_show_profile',
                        ['id' => $user->getId()],
                        UrlGeneratorInterface::ABSOLUTE_PATH
                    )
                ]
            );
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(
                FOSUserEvents::REGISTRATION_COMPLETED,
                new FilterUserResponseEvent($user, $request, $response)
            );
        }
        return $response;
    }

    private function security($user)
    {
        //---------------------------------security-----------------------------------------------
        // Access reserve au super admin
        $this->denyAccessUnlessGranted('ROLE_GESTIONNAIRE', $user, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$user instanceof Admin) {
            throw $this->createAccessDeniedException();
        }
        //-----------------------------------------------------------------------------------------
    }

//$class='APM\UserBundle\Entity\Admin';
//$em=$this->getManager($class);
//    /** @var Admin $admin */
//$admin= $em->createUser();
//$admin->setRoles($role);
//$admin->setEmail('yanno@avm.com');
//$em->updateUser($admin, true);

}