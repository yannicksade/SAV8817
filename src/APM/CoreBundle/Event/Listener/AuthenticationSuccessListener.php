<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 23/11/2017
 * Time: 16:58
 */

namespace APM\CoreBundle\Event\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    /**
     * Adding public data to the JWT response
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $data['data'] = array(
            'roles' => $user->getRoles(),
        );

        $event->setData($data);
    }
}