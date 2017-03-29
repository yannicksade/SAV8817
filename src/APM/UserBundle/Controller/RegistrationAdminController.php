<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegistrationAdminController extends Controller
{
    public function registerAction()
    {
        $class = 'APM\UserBundle\Entity\Admin';
        return $this
                    ->get('pugx_multi_user.registration_manager')
            ->register($class);


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