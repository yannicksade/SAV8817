<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 18/08/2017
 * Time: 19:50
 */

namespace APM\CoreBundle\Controller;


use APM\UserBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\LockedException;

class LockedScreenController extends Controller implements ContainerAwareInterface
{

    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        /** @var Utilisateur $user */
        $user = $this->getUser();
        $session->set('email', $user->getEmail());
        $session->set('username', $user->getUsername());
        $session->set('image', $user->getImage());

        throw new LockedException();
    }

    public function reloginAction(Request $request){
        $session = $request->getSession();
        $session->invalidate();
       return $this->redirectToRoute('fos_user_security_login');
    }

}