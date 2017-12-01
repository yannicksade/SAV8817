<?php

namespace APM\UserBundle\Controller;


use APM\UserBundle\Entity\Admin;
use APM\UserBundle\Entity\Utilisateur;
use APM\UserBundle\Entity\Utilisateur_avm;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class RegistrationUtilisateurAVMController
 * @package APM\UserBundle\Controller
 * @RouteResource("user")
 */
class RegistrationUserController extends FOSRestController
{
    /**
     * @Post("/register")
     * @param Request $request
     * @return JsonResponse | Response
     */
    public function registerAction(Request $request)
    {
        return $this->get('apm_user.registration_manager')->register(Utilisateur_avm::class, $request);

    }


    /**
     * @param Request $request
     * @return JsonResponse|Response
     * @Get("/confirmation-password")
     */
    public function registrationConfirmationAction(Request $request)
    {

        return $this->get('apm_user.registration_manager')->confirm(Utilisateur_avm::class, $request);
    }


}
