<?php

namespace APM\UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;

class RegistrationAdminController extends BaseController
{
    public function registerAction(Request $request)
    {
        $this->securitySecurity();
        $class = 'APM\UserBundle\Entity\Admin';
        return $this
                    ->get('pugx_multi_user.registration_manager')
            ->register($class);


    }

    private function securitySecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Access reserve au super admin
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
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

    /**
     * @param string $class
     * @return object
     */
//private function getManager($class){
//        $discriminator = $this->get('pugx_user.manager.user_discriminator');
//        $discriminator->setClass($class);
//       return $this->get('pugx_user_manager');
//
//    }
}