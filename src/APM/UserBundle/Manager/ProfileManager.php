<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 28/11/2017
 * Time: 09:19
 */

namespace APM\UserBundle\Manager;


use APM\UserBundle\Entity\Utilisateur;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProfileManager implements ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function updateUser($class, $request, $clearMissing = true, Utilisateur $user)
    {

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->discriminateAndGetManager($class);

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->container->get('pugx_multi_user.profile_form_factory');
        $form = $formFactory->createForm();

        if (!$request->request->has('current_password')) {
            return new JsonResponse([
                "status" => 400,
                "message" => $this->container->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
            ], Response::HTTP_BAD_REQUEST);
        }
        $form->setData($user);
        $form->submit($request->request->all(), $clearMissing);

        if (!$form->isValid()) {
            return $form;
        }

        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            return $user;
        }
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

        return $user;
    }

    private function discriminateAndGetManager($class)
    {
        $discriminator = $this->container->get('pugx_user.manager.user_discriminator');
        $discriminator->setClass($class);
        return $this->container->get('pugx_user_manager');
    }

}