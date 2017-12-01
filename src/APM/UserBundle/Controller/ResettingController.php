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

/**
 * Controller managing the resetting of the password.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ResettingController extends FOSRestController
{
    /**
     * @Post("/user/request")
     * @param Request $request
     * @return FormInterface|JsonResponse|Response
     */
    public function requestResetUserAction(Request $request)
    {
        return $this->get('apm_user.resetting_manager')->request(Utilisateur_avm::class, $request);
    }

    /**
     * Reset user password
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
     * @Post("/staff/request")
     * @param Request $request
     * @return FormInterface|JsonResponse|Response
     */
    public function requestResetStaffAction(Request $request)
    {
        return $this->get('apm_user.resetting_manager')->request(Admin::class, $request);
    }

    /**
     * Reset user password
     * @Post("/staff/confirm")
     * @param Request $request
     * @return FormInterface|JsonResponse|Response
     */
    public function confirmResetStaffAction(Request $request)
    {
        return $this->get('apm_user.resetting_manager')->confirm(Admin::class, $request);
    }

    /**
     * @Get("/get/form-user")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function getResetFormUserAction(Request $request)
    {
        return $this->get('apm_user.resetting_manager')->confirm(Utilisateur_avm::class, $request);
    }

    /**
     * @Get("/get/form-staff")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function getResetFormStaffAction(Request $request)
    {
        return $this->get('apm_user.resetting_manager')->confirm(Admin::class, $request);
    }


}