<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 06/06/2017
 * Time: 23:13
 */

namespace APM\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProfileUtilisateurAVMController extends Controller
{
    public function editAction()
    {
        $this->security();
        return $this->get('pugx_multi_user.profile_manager')
            ->edit('APM\UserBundle\Entity\Utilisateur_avm');
    }

    private function security()
    {
        //---------------------------------security-----------------------------------------------
        // Access reserve au utilisateur avm
        $this->denyAccessUnlessGranted(['ROLE_USERAVM'], null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //-----------------------------------------------------------------------------------------
    }
}