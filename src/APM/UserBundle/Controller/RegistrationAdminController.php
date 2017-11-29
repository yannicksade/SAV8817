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
        return $this->get('apm_user.registration_manager')->register(Admin::class, $request);
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