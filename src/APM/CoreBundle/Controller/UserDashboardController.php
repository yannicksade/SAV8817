<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 28/02/2017
 * Time: 01:17
 */

namespace APM\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserDashboardController extends Controller
{

    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        if ($session->has('previous_location')) {
            return $this->redirect($session->get('previous_location'));
        }
        return $this->render(':base/dashboard:layout.html.twig', [
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ]);
    }
}