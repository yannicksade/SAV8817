<?php

namespace APM\CoreBundle\Event\Listener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

    }

    /**
     * Adds additional data to the generated JWT
     *
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        /** @var $user \APM\UserBundle\Entity\Utilisateur */
        $user = $event->getUser();
        $payload = $event->getData();
        // add new data
        $payload['userId'] = $user->getId();
        $payload['username'] = $user->getUsername();
        //Override token expiration date calcul to be more flexible
        $expiration = new \DateTime("+1 day"); //
        //$expiration->setTime(0, 0);
        $payload['exp'] = $expiration->getTimestamp();
        // Add client ip to the encoded payload
        $request = $this->requestStack->getCurrentRequest();
        $payload['ip'] = $request->getClientIp();

        $event->setData($payload);
    }
}