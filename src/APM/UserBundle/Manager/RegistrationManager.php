<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 28/11/2017
 * Time: 04:06
 */

namespace APM\UserBundle\Manager;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationManager implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function register($class, $request)
    {
        // $this->security();
        /** @var $userManager UserManagerInterface */
        $userManager = $this->discriminateAndGetManager($class);

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->container->get('pugx_multi_user.registration_form_factory');
        $form = $formFactory->createForm();
        $form->setData($user);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }

            return $form;
        }

        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $userManager->updateUser($user);

        $location = $this->container->get("fos_rest.router")->generate('api_user_show', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_PATH);

        $response = new JsonResponse(
            [
                'msg' => $this->container->get('translator')->trans('registration.flash.user_created', [], 'FOSUserBundle'),
                'token' => $this->container->get('lexik_jwt_authentication.jwt_manager')->create($user)
            ],
            Response::HTTP_CREATED,
            [
                'location' => $location
            ]
        );

        $dispatcher->dispatch(
            FOSUserEvents::REGISTRATION_COMPLETED,
            new FilterUserResponseEvent($user, $request, $response)
        );

        return $response;
    }

    private function discriminateAndGetManager($class)
    {
        $discriminator = $this->container->get('pugx_user.manager.user_discriminator');
        $discriminator->setClass($class);
        return $this->container->get('pugx_user_manager');
    }
}