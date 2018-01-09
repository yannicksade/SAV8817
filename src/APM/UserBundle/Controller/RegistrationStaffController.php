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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
/**
 * Class RegistrationUtilisateurAVMController
 * @package APM\UserBundle\Controller
 * @RouteResource("staff")
 */
class RegistrationStaffController extends FOSRestController
{

    /**
     * @ApiDoc(
     * resource=true,
     * description="Create a staff account",
     *
     * parameters= {
     *      {"name"="imagefilex", "dataType"="integer", "required"= true, "description"="horizontal start point"},
     *      {"name"="imagefiley", "dataType"="integer", "required"= true, "description"="vertical start point"},
     *      {"name"="imagefilew", "dataType"="integer", "required"= true, "description"="width"},
     *      {"name"="imagefileh", "dataType"="integer", "required"= true, "description"="height"},
     *  },
     * input={
     *    "class"="APM\UserBundle\Form\Type\AdminFormType",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\FormTypeParser"
     *      }
     * },
     *  statusCodes={
     *     "output" = "Ends by sending a confirmation e-mail to the staff's address",
     *     200="Returned when successful",
     *     400="Returned when the data are not valid or an unknown error occurred",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default","profile"}
     * ),
     * @Post("/register")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function registerAction(Request $request)
    {
        try {
            $this->security($this->getUser());
            return $this->get('apm_user.registration_manager')->register(Admin::class, $request);
        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }
    }

    private function security($user)
    {
        //---------------------------------security-----------------------------------------------
        // Access reserve au super admin
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$user instanceof Admin) {
            throw $this->createAccessDeniedException();
        }
        //-----------------------------------------------------------------------------------------
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     * @Get("/confirmation-password")
     */
    public function registrationConfirmationAction(Request $request)
    {
        return $this->get('apm_user.registration_manager')->confirm(Admin::class, $request);
    }

}
