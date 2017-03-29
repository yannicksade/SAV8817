<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 05/03/2017
 * Time: 06:01
 */

namespace APM\CoreBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AuditeurController extends Controller
{
    public function indexAction()
    {

        return new Response("Pages des Auditeurs");
    }
}