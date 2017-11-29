<?php

namespace APM\UserBundle\Controller;


use APM\UserBundle\Entity\Utilisateur_avm;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use FOS\RestBundle\Controller\Annotations\Post;

/**
 * Class RegistrationUtilisateurAVMController
 * @package APM\UserBundle\Controller
 *
 */
class RegistrationUserController extends FOSRestController
{
    /**
     * @Post("/register", name="_user")
     * @param Request $request
     * @return FormInterface|JsonResponse|Response
     */
    public function registerAction(Request $request)
    {
        /** @var Utilisateur_avm $user */
        return $this->get('apm_user.registration_manager')->register(Utilisateur_avm::class, $request);
    }

}
