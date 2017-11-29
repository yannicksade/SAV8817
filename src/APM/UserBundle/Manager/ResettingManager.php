<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 29/11/2017
 * Time: 08:31
 */

namespace APM\UserBundle\Manager;


use APM\UserBundle\Entity\Utilisateur;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResettingManager implements ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function request($class, $request)
    {
        $username = $request->request->get('username');
        /** @var UserManagerInterface $em */
        $em = $this->discriminateAndGetManager($class);
        /** @var $user UserInterface */
        $user = $em->findUserByUsernameOrEmail($username);

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        /* Dispatch init event */
        $event = new GetResponseNullableUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        if (null === $user) {
            return new JsonResponse(
                'User not recognised',
                JsonResponse::HTTP_FORBIDDEN
            );
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_REQUEST, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return new JsonResponse(
                $this->container->get('translator')->trans('resetting.password_already_requested', [], 'FOSUserBundle'),
                JsonResponse::HTTP_FORBIDDEN
            );
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator TokenGeneratorInterface */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        /* Dispatch confirm event */
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_CONFIRM, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $em->updateUser($user);


        /* Dispatch completed event */
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        return new JsonResponse(
            $this->container->get('translator')->trans(
                'resetting.check_email',
                ['%tokenLifetime%' => floor($this->container->getParameter('fos_user.resetting.token_ttl') / 3600)],
                'FOSUserBundle'
            ),
            JsonResponse::HTTP_OK
        );
    }

    private function discriminateAndGetManager($class)
    {
        $discriminator = $this->container->get('pugx_user.manager.user_discriminator');
        $discriminator->setClass($class);
        return $this->container->get('pugx_user_manager');
    }

    public function confirm($class, $request)
    {
        $token = $request->request->get('token', null);

        if (null === $token) {
            return new JsonResponse('You must submit a token.', JsonResponse::HTTP_BAD_REQUEST);
        }

        /** @var $userManager UserManagerInterface */
        $userManager = $this->discriminateAndGetManager($class);

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->container->get('fos_user.resetting.form.factory');

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            return new JsonResponse(
            // no translation provided for this in \FOS\UserBundle\Controller\ResettingController
                sprintf('The user with "confirmation token" does not exist for value "%s"', $token),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            return new JsonResponse(
                $this->container->get('translator')->trans('resetting.flash.success', [], 'FOSUserBundle'),
                JsonResponse::HTTP_OK
            );
        }

        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

        return new JsonResponse(
            $this->container->get('translator')->trans('resetting.flash.success', [], 'FOSUserBundle'),
            JsonResponse::HTTP_OK
        );
    }

    public function change($class, $request, $user)
    {
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->container->get('fos_user.change_password.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        /** @var $userManager UserManagerInterface */
        $userManager = $this->discriminateAndGetManager($class);

        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            return new JsonResponse(
                $this->container->get('translator')->trans('change_password.flash.success', [], 'FOSUserBundle'),
                JsonResponse::HTTP_OK
            );
        }

        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

        return new JsonResponse(
            $this->container->get('translator')->trans('change_password.flash.success', [], 'FOSUserBundle'),
            JsonResponse::HTTP_OK
        );
    }

}