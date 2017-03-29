<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 05/03/2017
 * Time: 07:11
 */

namespace APM\CoreBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TransportController extends Controller
{
    public function indexAction()
    {

        return new Response("Pages des Transporteurs");
    }
}