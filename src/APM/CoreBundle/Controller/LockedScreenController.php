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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;


class LockedScreenController extends Controller implements RememberMeServicesInterface
{
    public function indexAction(Request $request)
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();
        /** @var Session $session */
        $session = $request->getSession();
        $session->set('username', $user->getUsername());
        $session->set('image', $user->getImage());
        $authErrorKey = Security::AUTHENTICATION_ERROR;
        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }
        $lastUsernameKey = Security::LAST_USERNAME;

        // last username entered by the user
        if(null !== $session){
           $lastUsername = $session->get('username');
            $image = $session->get('image');
        }else{
            $lastUsername ='';
            $image = '';
        }
        $csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

        //$session->save();
        //$session->clear();
        return $this->render('@FOSUser/Security/locked-screen.html.twig', array(
            'last_username' => $lastUsername,
            'image' => $image,
            'error' => $error,
            'csrf_token' => $csrfToken,
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    public function autoLogin(Request $request)
    {
      //$request->attributes->get(self::COOKIE_ATTR_NAME);
    }

    public function loginFail(Request $request)
    {
        // TODO: Implement loginFail() method.
    }

    public function loginSuccess(Request $request, Response $response, TokenInterface $token)
    {
        // TODO: Implement loginSuccess() method.
    }
}