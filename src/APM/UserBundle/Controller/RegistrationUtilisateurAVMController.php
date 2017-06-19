<?php

namespace APM\UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegistrationUtilisateurAVMController extends Controller
{
    public function registerAction()
    {
        return $this
                    ->get('pugx_multi_user.registration_manager')
                    ->register('APM\UserBundle\Entity\Utilisateur_avm');

    }


}
