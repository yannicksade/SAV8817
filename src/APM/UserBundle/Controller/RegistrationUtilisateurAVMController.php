<?php

namespace APM\UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RouteResource;
class RegistrationUtilisateurAVMController extends Controller
{
    /**
     * @Post("/register/user", name="_user")
     */
    public function registerAction()
    {
        return $this
                    ->get('pugx_multi_user.registration_manager')
                    ->register('APM\UserBundle\Entity\Utilisateur_avm');

    }


}
