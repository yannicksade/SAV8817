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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class RegistrationUtilisateurAVMController
 * @package APM\UserBundle\Controller
 * @RouteResource("user")
 */
class RegistrationUserController extends FOSRestController
{
    /**
     * @ApiDoc(
     * resource=true,
     * description="Create a user account",
     *  input={
     *   "class"="APM\UserBundle\Entity\Utilisateur_avm",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *    }
     *     },
     *  statusCodes={
     *     "output" = "Ends by sending a confirmation e-mail to your address",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *      views={"default","login"}
     * ),
     * @Post("/register")
     * @param Request $request
     * @return JsonResponse | Response
     */
    public function registerAction(Request $request)
    {
        return $this->get('apm_user.registration_manager')->register(Utilisateur_avm::class, $request);

    }


    /**
     * @ApiDoc(
     * resource=true,
     * description="Activates your account by email",
     * requirements = {
     *   {"name"="token", "dataType"="string", "requirement"="\D+", "required"=true, "description"="token ..."}
     * },
     *  statusCodes={
     *     "output" = "HTML confirmation template",
     *     200="Returned when successful",
     *     400="Returned when the data are not valid or an unknown error occurs",
     *     404="Returned when the specified resource is not found with the provided token",
     * },
     *     views={"default","login"}
     * ),
     * @param Request $request
     * @return JsonResponse|Response
     * @Get("/confirmation-password")
     */
    public function registrationConfirmationAction(Request $request)
    {

        return $this->get('apm_user.registration_manager')->confirm(Utilisateur_avm::class, $request);
    }


}
