<?php

namespace APM\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function getAction()
    {
        return $this->redirectToRoute("apm_core_user-dashboard_index");
    }
}
