<?php

namespace APM\CoreBundle\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommissionController extends Controller
{
    public function indexAction()
    {
        return $this->render('::base/platform/page/commission.html.twig');
    }
}
