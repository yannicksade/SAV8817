<?php

namespace APM\CoreBundle\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DocumentationController extends Controller
{
    public function indexAction()
    {
        return $this->render('::base/platform/page/documentation.html.twig');
    }
}
