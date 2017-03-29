<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 28/02/2017
 * Time: 01:17
 */

namespace APM\CoreBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserDashboardController extends Controller
{
//traite ici la disposition de l'utilisateur dans son espace d'administration.
    public function showAction()
    {
        return $this->render(':client/dashboard:user-dashboard.html.twig');
    }
}