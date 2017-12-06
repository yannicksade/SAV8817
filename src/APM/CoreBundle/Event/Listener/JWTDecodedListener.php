<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 22/11/2017
 * Time: 05:12
 */

namespace APM\CoreBundle\Event\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTDecodedListener
{
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

    }

    /** Check client ip the decoded payload
     * @param JWTDecodedEvent $event
     *
     * @return void
     */
    public function onJWTDecoded(JWTDecodedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $payload = $event->getPayload();
        if (!isset($payload['ip']) || $payload['ip'] != $request->getClientIp()) {
            $event->markAsInvalid();
        }
    }
}