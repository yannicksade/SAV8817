<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 22/11/2017
 * Time: 05:53
 */

namespace APM\CoreBundle\Event\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class JWTNotFoundListener
{
    /**
     * @param JWTNotFoundEvent $event
     */
    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        $data = [
            'status' => '403 Forbidden',
            'message' => 'Missing token',
        ];

        $response = new JsonResponse($data, 403);

        $event->setResponse($response);
    }
}