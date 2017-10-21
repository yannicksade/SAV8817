<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 28/02/2017
 * Time: 01:17
 */

namespace APM\CoreBundle\Controller;

use APM\UserBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\EventListener\LastLoginListener;
class UserDashboardController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexAction(Request $request)
    {
        $json = array();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $json['status'] = false;
            return $this->json(json_encode($json), 401);
        }
        /** @var Utilisateur $user */
        $user = $this->getUser();
        $session = $request->getSession();
        $json['user'] = array(
            'id' => $user->getId(),
            'code' => $user->getCode(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img') . $user->getImage(),
        );
        $json['status'] = true;
        $json['prev_url'] = $session->has('previous_location') ? $session->get('previous_location') : '/';
        return $this->json(json_encode($json), 200);
    }
}