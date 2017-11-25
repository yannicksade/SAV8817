<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 22/11/2017
 * Time: 05:49
 */

namespace APM\CoreBundle\Event\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;

class JWTInvalidListener
{
    /**
     * @param JWTInvalidEvent $event
     */
    public function onJWTInvalid(JWTInvalidEvent $event)
    {
        $response = new JWTAuthenticationFailureResponse('Your token is invalid, please login again to get a new one', 403);

        $event->setResponse($response);
    }
}