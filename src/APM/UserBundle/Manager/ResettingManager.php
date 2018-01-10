<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 29/11/2017
 * Time: 08:31
 */

namespace APM\UserBundle\Manager;

use APM\UserBundle\Entity\Admin;
use Doctrine\DBAL\Exception\ConstraintViolationException;
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

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        $this->container->get('apm_user.rest_mailer')->sendResettingEmailMessage($user);
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

    public function confirm($class, Request $request)
    {
        /** @var $formFactory FactoryInterface */
        $formFactory = $this->container->get('fos_user.resetting.form.factory'); // the form is apm_user_resetting_form
        $form = $formFactory->createForm();
        $token = '';
        if ($request->query->has('token')) {
            $token = $request->query->get('token');
            $form->remove('token');
        } elseif ($request->request->has('token')) {
            $token = $request->request->get('token');
        }
        if ($token == null) {
            return new JsonResponse('You must submit a token.', JsonResponse::HTTP_BAD_REQUEST);
        }

        /** @var $userManager UserManagerInterface */
        $userManager = $this->discriminateAndGetManager($class);
        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            return new JsonResponse(
            // no translation provided for this in \FOS\UserBundle\Controller\ResettingController
                sprintf('The user with "confirmation token" does not exist for value: "%s"', $token),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $form->setData($user);

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');
        $view = (new view(null, 200))->setFormat("html");
        if ($request->isMethod('POST')) {
            if ($request->request->has('token')) {
                $data = $request->request->has($form->getName()) ? $request->request->get($form->getName()) : $data[$form->getName()] = array();
                $form->submit($data);
            } else {
                $form->handleRequest($request);
            }

            if ($form->isValid()) {
                $data = array(
                    "username" => $user->getUsername(),
                    'message' => $this->container->get('translator')->trans('resetting.flash.success', [], 'FOSUserBundle'),
                    'target' => "http://localhost:4200/"
                );
                $view->setTemplate("@FOSUser/Registration/confirmed.html.twig")->setTemplateData($data);
                $myResponse = $this->container->get("fos_rest.view_handler")->handle($view);

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);
                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    return $myResponse;
                }

                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $myResponse;
            } else {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->container->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
        }
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        $path = $user instanceof Admin ? "confirm_reset_staff" : "confirm_reset_user";
        $data = array(
            "username" => $user->getUsername(),
            "form" => $form->createView(),
            "token" => $token,
            "route" => $path
        );

        $view->setTemplate("@FOSUser/Resetting/reset.html.twig")
            ->setTemplateData($data);

        return $this->container->get("fos_rest.view_handler")->handle($view);

    }

    public function change($class, $request, $user)
    {
        try {
            /** @var $dispatcher EventDispatcherInterface */
            $dispatcher = $this->container->get('event_dispatcher');

            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

            if (null !== $event->getResponse()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->container->get('translator')->trans("Bad response", [], 'FOSUserBundle')
                ]);
            }

            /** @var $formFactory FactoryInterface */
            $formFactory = $this->container->get('fos_user.change_password.form.factory');

            $form = $formFactory->createForm();
            $form->setData($user);
            $data = $request->request->has($form->getName()) ? $request->request->get($form->getName()) : $data[$form->getName()] = array();
            $form->submit($data);

            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->container->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
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

        } catch (ConstraintViolationException $cve) {
            return [
                "status" => 400,
                "message" => $this->container->get('translator')->trans("impossible d'enregistrer, vérifiez vos données", [], 'FOSUserBundle')
            ];
        }
    }

}