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

class GestionController extends Controller
{
    public function indexAction()
    {

        return new Response("Pages des Administrateurs chargés de  gérer le business");
    }
}