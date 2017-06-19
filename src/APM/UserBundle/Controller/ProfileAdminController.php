<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 06/06/2017
 * Time: 23:27
 */

namespace APM\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class ProfileAdminController extends Controller
{

    public function editAction(Request $request)
    {
        $this->security();
        return $this->get('pugx_multi_user.profile_manager')
            ->edit('APM\UserBundle\Entity\Admin');
    }

    private function security()
    {
        //---------------------------------security-----------------------------------------------
        // Access reserve au super admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //-----------------------------------------------------------------------------------------
    }

}


