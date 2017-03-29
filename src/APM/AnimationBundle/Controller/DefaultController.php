<?php

namespace APM\AnimationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        //return $this->render('APMAnimationBundle:crud:index.html.twig');
        return new Response('Hello World');
    }
}
