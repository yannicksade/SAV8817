<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 23/11/2017
 * Time: 16:52
 */

namespace APM\CoreBundle\Event\Listener;

use APM\UserBundle\Entity\Utilisateur;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;

class JWTAuthenticatedListener
{

    /**
     * @param JWTAuthenticatedEvent $event
     *
     * @return void
     */
    public function onJWTAuthenticated(JWTAuthenticatedEvent $event)
    {
        $token = $event->getToken();
        //$payload = $event->getPayload();
        if ($token && $token->isAuthenticated()) {
            /** @var Utilisateur $user */
            //$user = $token->getUser();
            //$user->setLastLogin(new \DateTime());
        }
        //$token->setAttribute('UUID', $payload['UUID']);

    }
}