<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 23/11/2017
 * Time: 16:52
 */

namespace APM\CoreBundle\Event\Listener;

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
        $payload = $event->getPayload();

        $token->setAttribute('UUID', $payload['UUID']);
        $token->isAuthenticated();
    }
}