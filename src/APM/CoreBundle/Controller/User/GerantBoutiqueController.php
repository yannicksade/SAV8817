<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 05/03/2017
 * Time: 07:11
 */

namespace APM\CoreBundle\Controller\User;


use Symfony\Component\HttpFoundation\Response;

class GerantBoutiqueController
{
    public function indexAction()
    {

        return new Response("Pages des Propriétaires ou gérants de boutiques");
    }
}