<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 05/03/2017
 * Time: 04:32
 */

namespace APM\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class GestionController extends Controller
{
    public function getAction()
    {

        return new Response("Pages des Administrateurs chargés de  gérer le business");
    }
}