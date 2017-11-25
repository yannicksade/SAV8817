<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 22/11/2017
 * Time: 05:38
 */

namespace APM\CoreBundle\Event\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTEncodedEvent;

class JWTEncodedListener
{
    /**Obtain JWT string
     * @param JWTEncodedEvent $event
     */
    public function onJwtEncoded(JWTEncodedEvent $event)
    {
        $token = $event->getJWTString();
    }
}