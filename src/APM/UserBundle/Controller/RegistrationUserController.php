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
        $response = $this->get('apm_user.registration_manager')->register(Utilisateur_avm::class, $request);

        if (is_object($response) && $response instanceof Utilisateur_avm) {
            $user = $response;
            $response = new JsonResponse(
                [
                    'msg' => $this->get('translator')->trans('registration.flash.user_created', [], 'FOSUserBundle'),
                    'token' => $this->get('lexik_jwt_authentication.jwt_manager')->create($user)
                ],
                Response::HTTP_CREATED,
                [
                    'location' => $this->generateUrl(
                        'api_user_show_profile',
                        ['id' => $user->getId()],
                        UrlGeneratorInterface::ABSOLUTE_PATH
                    )
                ]
            );
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(
                FOSUserEvents::REGISTRATION_COMPLETED,
                new FilterUserResponseEvent($user, $request, $response)
            );
        }
        return $response;
    }

}
