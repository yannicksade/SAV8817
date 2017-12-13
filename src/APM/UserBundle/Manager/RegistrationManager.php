<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 28/11/2017
 * Time: 04:06
 */

namespace APM\UserBundle\Manager;


use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $user->setEnabled(false);

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

            return new JsonResponse("invalid data", 400);
        }
        $event = new FormEvent($form, $request);

        if ($event->getResponse()) {
            return $event->getResponse();
        }
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
        $userManager->updateUser($user);

        return new JsonResponse(
            $this->container->get('translator')->trans(
                'registration.check_email',
                ['%email%' => $user->getEmail()],
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

    public function confirm($class, Request $request)
    {
        if (!$request->query->has('token')) {
            return new JsonResponse('You must submit a token.', JsonResponse::HTTP_BAD_REQUEST);
        }
        $token = $request->query->get('token');
        /** @var $userManager UserManagerInterface */
        $userManager = $this->discriminateAndGetManager($class);
        $user = $userManager->findUserByConfirmationToken($token);
        if (null === $user) {
            return new JsonResponse(
                sprintf('The user with "confirmation token" does not exist for value "%s"', $token),
                JsonResponse::HTTP_BAD_REQUEST);
        }
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        //$location = $this->container->get("fos_rest.router")->generate('api_user_show', ['id' => $user->getId()], UrlGeneratorInterface::RELATIVE_PATH);
        $data = array(
            "username" => $user->getUsername(),
            'message' => $this->container->get('translator')->trans('registration.flash.user_created', [], 'FOSUserBundle'),
            'target' => "http://localhost:4200/"
        );

        $view = new view(null, 200);
        $view->setTemplate("@FOSUser/Registration/confirmed.html.twig")
            ->setTemplateData($data)
            ->setFormat("html");
        $myResponse = $this->container->get("fos_rest.view_handler")->handle($view);

        if (null === $response = $event->getResponse()) {
            return $myResponse;
        }

        $dispatcher->dispatch(
            FOSUserEvents::REGISTRATION_COMPLETED,
            new FilterUserResponseEvent($user, $request, $response)
        );

        return $myResponse;
    }
}