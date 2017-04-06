<?php

namespace APM\UserBundle\Controller;


use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;

class RegistrationUtilisateurAVMController extends BaseController
{
    public function registerAction(Request $request)
    {
        return $this
                    ->get('pugx_multi_user.registration_manager')
                    ->register('APM\UserBundle\Entity\Utilisateur_avm');
    }

}
