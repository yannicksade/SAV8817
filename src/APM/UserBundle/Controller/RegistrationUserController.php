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
     * @return JsonResponse
     */
    public function registerAction(Request $request)
    {
        /** @var Utilisateur_avm $user */
        $response = $this->get('apm_user.registration_manager')->register(Utilisateur_avm::class, $request);

        if (is_object($response) && $response instanceof Utilisateur_avm) {
            $user = $response;
            return new JsonResponse(
                array(
                    "msg" => "A token has been sent to" . $user->getEmail() . "please check your email to activate your account!"
                ),
                Response::HTTP_CONTINUE
            );
        } elseif ($response instanceof FormInterface) {
            return new JsonResponse(
                array(
                    "msg" => "data invalid"
                ),
                Response::HTTP_BAD_REQUEST
            );
        } else {
            return new JsonResponse(
                array(
                    "msg" => "unknow error"
                ),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Post("/confirmation-password")
     */
    public function registrationConfirmationAction(Request $request)
    {

        return $this->get('apm_user.registration_manager')->confirm(Utilisateur_avm::class, $request);
    }


}
