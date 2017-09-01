<?php

namespace APM\CoreBundle\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ConseilleController extends Controller
{
    public function indexAction()
    {
        return $this->render('::base/platform/page/conseille.html.twig');
    }
    public function detailAction()
    {
        return $this->render('::base/platform/conseille_detail.html.twig');
    }
}
