<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Admin;
use APM\UserBundle\Entity\Utilisateur_avm;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Controller managing the resetting of the password.
 *
 * @author yannick sade <yannicksade@gmail.com>
 */
class ResettingController extends FOSRestController
{
    /**
     * @ApiDoc(
     * resource=true,
     * description="Reset user password ",
     * parameters = {
     *     {"name"="username", "dataType"="string", "required"=true, "format"="yannick | ysade@avm.com", "description"="username or email"}
     * },
     *  statusCodes={
     *     "output" = "Ends by returning the user reset form",
     *     200="Returned when successful",
     *     400="Returned when the data are not valid or an unknown error occurs",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     *  },
     *     views={"default","profile"}
     * ),
     * @Post("/user/request")
     * @param Request $request
     * @return FormInterface|JsonResponse|Response
     */
    public function requestResetUserAction(Request $request)
    {
        return $this->get('apm_user.resetting_manager')->request(Utilisateur_avm::class, $request);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Reset staff password",
     *
     * parameters = {
     *     {"name"="username", "dataType"="string", "required"=true, "format"="yannick | ysade@avm.com", "description"="username or email"}
     * },
     *  statusCodes={
     *     "output" = "Ends by returning the staff reset form",
     *     200="Returned when successful",
     *     400="Returned when the data are not valid or an unknown error occurs",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     *  },
     *     views={"default","profile"}
     * ),
     * @Post("/staff/request")
     * @param Request $request
     * @return FormInterface|JsonResponse|Response
     */
    public function requestResetStaffAction(Request $request)
    {
        return $this->get('apm_user.resetting_manager')->request(Admin::class, $request);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Confirm resetting of staff password from e-mail",
     *  statusCodes={
     *     200="Returned when successful",
     *     400="Returned when the data are not valid or an unknown error occurs",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     *  },
     *  requirements = {
     *      {"name"="token", "dataType"="password", "requirement"="\D+", "required"=true, "description"="token..."},
     *   },
     *  input={
     *     "class"="APM\UserBundle\Form\Type\ResettingFormType",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\FormTypeParser"
     *      }
     *  },
     *     views={"default","profile"}
     * ),
     * @Post("/user/confirm")
     * @param Request $request
     * @return FormInterface|JsonResponse|Response
     */
    public function confirmResetUserAction(Request $request)
    {
        return $this->get('apm_user.resetting_manager')->confirm(Utilisateur_avm::class, $request);
    }


    //----------------------------------- staff ------------------------------------------------

    /**
     * @ApiDoc(
     * resource=true,
     * description="Confirm resetting of staff password from e-mail",
     *  statusCodes={
     *     200="Returned when successful",
     *     400="Returned when the data are not valid or an unknown error occurs",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     *  },
     *  requirements = {
     *      {"name"="token", "dataType"="password", "requirement"="\D+", "required"=true, "description"="token..."},
     *   },
     *  input={
     *     "class"="APM\UserBundle\Form\Type\ResettingFormType",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\FormTypeParser"
     *      }
     *  },
     *     views={"default","profile"}
     * ),
     * @Post("/staff/confirm")
     * @param Request $request
     * @return FormInterface|JsonResponse|Response
     */
    public function confirmResetStaffAction(Request $request)
    {
        return $this->get('apm_user.resetting_manager')->confirm(Admin::class, $request);
    }

    /**
     * @Get("/get/confirm/staff-form")
     * @param Request $request
     * @return FormInterface|JsonResponse|Response
     */
    public function getResetFormStaffAction(Request $request)
    {
        return $this->get('apm_user.resetting_manager')->confirm(Admin::class, $request);
    }

    /**
     * @Get("/get/confirm/user-form")
     * @param Request $request
     * @return FormInterface|JsonResponse|Response
     */
    public function getResetFormUserAction(Request $request)
    {
        return $this->get('apm_user.resetting_manager')->confirm(Utilisateur_avm::class, $request);
    }
}