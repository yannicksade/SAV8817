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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\UserBundle\Form\Type\ChangePasswordFormType;
class ProfileController extends FOSRestController
{
    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve user profile.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements= {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description" = "user id"}
     * },
     * output={
     *   "class"="APM\UserBundle\Entity\Utilisateur",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_user_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "Return user or staff profile",
     *     200="Returned when successful",
     *     400="Returned when the data are not valid or an unknown error occurs",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "profile"}
     * )
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
     * @ApiDoc(
     * resource=true,
     * description="Update user profile.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements= {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description" = "user id"}
     * },
     * input={
     *    "class"="APM\UserBundle\Entity\Utilisateur_avm",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *    }
     * },
     * output={
     *   "class"="APM\UserBundle\Entity\Utilisateur_avm",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_user_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "Return user profile",
     *     200="Returned when successful",
     *     400="Returned when the data are not valid or an unknown error occurs",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "profile"}
     * )
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
     * @ApiDoc(
     * resource=true,
     * description="Update staff profile.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements= {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description" = "staff id"}
     * },
     * input={
     *    "class"="APM\UserBundle\Entity\Admin",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *    }
     * },
     * output={
     *   "class"="APM\UserBundle\Entity\Admin",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_user_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "Return user profile",
     *     200="Returned when successful",
     *     400="Returned when the data are not valid or an unknown error occurs",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "profile"}
     * )
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
     * @ApiDoc(
     * resource=true,
     * description="Change user password.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements= {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description" = "user id"}
     * },
     * parameters={
     *    {"name"="current_password", "required"=true, "dataType"="password", "requirement"="\D+", "description"="your current password"},
     *    {"name"="plainPassword[first]", "required"=true, "dataType"="password", "requirement"="\D+", "description"="your new password"},
     *    {"name"="plainPassword[second]", "required"=true, "dataType"="password", "requirement"="\D+", "description"="confirmation password"}
     * },
     * statusCodes={
     *     200="Returned when successful",
     *     400="Returned when the data are not valid or an unknown error occurs",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "profile"}
     * )
     *
     * @Post("/change-password/user/{id}")
     * @param Request $request
     * @param Utilisateur_avm $user
     * @return FormInterface|JsonResponse|Response
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
     * @ApiDoc(
     * resource=true,
     * description="Change staff password .",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements= {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description" = "staff id"}
     * },
     * parameters={
     *    {"name"="current_password", "required"=true, "dataType"="password", "requirement"="\D+", "description"="your current password"},
     *    {"name"="plainPassword[first]", "required"=true, "dataType"="password", "requirement"="\D+", "description"="your new password"},
     *    {"name"="plainPassword[second]", "required"=true, "dataType"="password", "requirement"="\D+", "description"="confirmation password"}
     * },
     * statusCodes={
     *     200="Returned when successful",
     *     400="Returned when the data are not valid or an unknown error occurs",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "profile"}
     * )
     *
     * @Post("/change-password/staff/{id}")
     * @param Request $request
     * @param Admin $user
     * @return FormInterface|JsonResponse|Response
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