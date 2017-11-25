<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 22/11/2017
 * Time: 06:01
 */

namespace APM\CoreBundle\Event\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;

class JWTExpiredListener
{
    /**
     * @param JWTExpiredEvent $event
     */
    public function onJWTExpired(JWTExpiredEvent $event)
    {
        /** @var JWTAuthenticationFailureResponse $response */
        $response = $event->getResponse();
        //$event->getException()->setToken(null);
        $response->setMessage('Your token is expired, please renew it.');
    }
}