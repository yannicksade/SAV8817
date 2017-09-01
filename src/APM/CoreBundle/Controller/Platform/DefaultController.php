<?php

namespace APM\CoreBundle\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('::base/platform/page/index.html.twig');
    }
    public function connectAction()
    {
        return $this->render('::base/platform/page/connexion.html.twig');
    }
    public function emptyAction()
    {
        return $this->render('::base/platform/page/i.html.twig');
    }
}
