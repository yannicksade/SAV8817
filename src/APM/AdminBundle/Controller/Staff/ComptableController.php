<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 05/03/2017
 * Time: 06:18
 */

namespace APM\AdminBundle\Controller\Staff;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ComptableController extends Controller
{
    public function getAction()
    {

        return new Response("Pages des Comptables");
    }
}