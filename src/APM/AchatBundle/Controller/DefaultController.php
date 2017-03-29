<?php
namespace APM\AchatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 19/01/2017
 * Time: 15:09
 */
class DefaultController extends Controller
{
    public function indexAction()
    {

        return new Response($this->get('apm_service.test')->getResult());
    }

}