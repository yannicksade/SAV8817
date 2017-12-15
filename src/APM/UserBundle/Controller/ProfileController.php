<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 28/11/2017
 * Time: 09:17
 */

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Admin;
use APM\UserBundle\Entity\Utilisateur_avm;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use APM\UserBundle\Entity\Utilisateur;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends FOSRestController
{
    /**
     * Show the user.
     * @param Utilisateur $user
     * @return JsonResponse
     * @Get("/show/profile/{id}")
     */
    public function showAction(Utilisateur $user)
    {
        $data = $this->get('apm_core.data_serialized')->getFormalData($user, ["owner_user_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @param Request $request
     * @param Utilisateur_avm $user
     * @return View|JsonResponse|Response
     *
     * @Patch("/patch/profile/user/{id}")
     */
    public function patchUserAction(Request $request, Utilisateur_avm $user)
    {
        try {
            $this->securityUser($user);
            /** @var Utilisateur_avm $utilisateur */
            $response = $this->get('apm_user.update_profile_manager')->updateUser(Utilisateur_avm::class, $request, false, $user);
            if (is_object($response) && $response instanceof Utilisateur_avm) {
                $utilisateur = $response;
                return $this->routeRedirectView('api_user_show', ['id' => $utilisateur->getId()], Response::HTTP_NO_CONTENT);
            }
            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);

        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }
    }

    private function securityUser($user)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', $user, 'This user does not have access to this section.');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //-----------------------------------------------------------------------------------------
    }

    /**
     * @param Request $request
     * @param Admin $user
     * @return View|JsonResponse
     *
     * @Patch("/patch/profile/staff/{id}")
     */
    public function patchStaffAction(Request $request, Admin $user)
    {
        try {
            $this->securityStaff($user);
            $response = $this->get('apm_user.update_profile_manager')->updateUser(Admin::class, $request, false, $user);
            if (is_object($response) && $response instanceof Admin) {
                $utilisateur = $response;
                return $this->routeRedirectView('api_user_show', ['id' => $utilisateur->getId()], Response::HTTP_NO_CONTENT);
            }

            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);

        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }
    }

    private function securityStaff($user)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_STAFF', $user, 'This user does not have access to this page.');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //-----------------------------------------------------------------------------------------
    }

    /**
     * Change user password
     *
     * @Post("/change-password/user/{id}")
     * @param Request $request
     * @param Utilisateur_avm $user
     * @return FormInterface|JsonResponse
     */
    public function changepasswordUserAction(Request $request, Utilisateur_avm $user)
    {
        try {
            $this->securityUser($user);
            if ($user !== $this->getUser()) {
                throw new AccessDeniedHttpException("This user does not have access to this section");
            }
            return $this->get('apm_user.resetting_manager')->change(Utilisateur_avm::class, $request, $user);

        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }
    }

    /**
     * Change staff password
     *
     * @Post("/change-password/staff/{id}")
     * @param Request $request
     * @param Admin $user
     * @return FormInterface|JsonResponse
     */
    public function changepasswordStaffAction(Request $request, Admin $user)
    {
        try {
            $this->securityStaff($user);
            if ($user !== $this->getUser()) {
                throw new AccessDeniedHttpException("This user does not have access to this section");
            }
            return $this->get('apm_user.resetting_manager')->change(Admin::class, $request, $user);

        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }
    }

}