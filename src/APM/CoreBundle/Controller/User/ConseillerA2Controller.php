<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 05/03/2017
 * Time: 07:10
 */

namespace APM\CoreBundle\Controller\User;


use Symfony\Component\HttpFoundation\Response;

class ConseillerA2Controller
{
    public function indexAction()
    {

        return new Response("Pages des Conseiller (A2) agrées");
    }

}