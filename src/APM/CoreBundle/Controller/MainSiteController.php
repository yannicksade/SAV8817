<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 28/02/2017
 * Time: 02:31
 */

namespace APM\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class MainSiteController extends Controller
{
    public function getAction(Request $request)
    {

        return new Response('Hello: Plateforme! Main site');
    }

}