<?php

namespace APM\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class RegistrationAdminController
 * @RouteResource("register")
 */
class RegistrationAdminController extends Controller
{
    /**
     * @Post("/register/staff", name="_staff")
     */
    public function registerAction(Request $request)
    { //accessible uniquement au super-administrateur
        $this->security();
        $class = 'APM\UserBundle\Entity\Admin';
        return $this
                    ->get('pugx_multi_user.registration_manager')
            ->register($class);

    }

    private function security()
    {
        //---------------------------------security-----------------------------------------------
        // Access reserve au super admin
        $this->denyAccessUnlessGranted('ROLE_GESTIONNAIRE', null, 'Unable to access this page!');
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
     *  Recupération de l'entity manager pour la discrimination
     * @param string $class
     * @return object
     */
    /*private function getManager($class){
            $discriminator = $this->get('pugx_user.manager.user_discriminator');
            $discriminator->setClass($class);
           return $this->get('pugx_user_manager');

        }*/

}