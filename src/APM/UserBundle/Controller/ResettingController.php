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

    /**
     * Change user password
     *
     * @Post("/user/change/{id}")
     * @param Request $request
     * @param Utilisateur_avm $user
     * @return FormInterface|JsonResponse
     */
    public function changeResetUserAction(Request $request, Utilisateur_avm $user)
    {
        if ($user !== $this->getUser()) {
            throw new AccessDeniedHttpException();
        }
        return $this->get('apm_user.resetting_manager')->change(Utilisateur_avm::class, $request, $user);
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
     * Change user password
     *
     * @Post("/staff/change/{id}")
     * @param Request $request
     * @param Admin $user
     * @return FormInterface|JsonResponse
     */
    public function changeResetStaffAction(Request $request, Admin $user)
    {
        if ($user !== $this->getUser()) {
            throw new AccessDeniedHttpException();
        }
        return $this->get('apm_user.resetting_manager')->change(Admin::class, $request, $user);
    }

}