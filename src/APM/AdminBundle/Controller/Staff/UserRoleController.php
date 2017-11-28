<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 31/03/2017
 * Time: 16:47
 */

namespace APM\AdminBundle\Controller\Staff;


use APM\CoreBundle\Form\Type\UsersRolePromptType;
use APM\UserBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class UserRoleController
 * @RouteResource("role", pluralize=false)
 */
class UserRoleController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Put("/grant/role")
     */
    public function grantRoleAction(Request $request)
    {
        $form = $this->createForm(UsersRolePromptType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $data = $form->getData();
            $role = $data['role'];
            $utilisateurs = $data['utilisateurs'];
            $isAddRole = $data['ajouterRole'];
            $isDeleteRole = $data['supprimerRole'];

            if ($role != null) { //Lister seulement. si aucun role n'est choisir
                // Ajouter,  remplacer ou supprimer un Role des utilisateur selectionnés
                /** @var Utilisateur $userGranted */
                foreach ($utilisateurs as $userGranted) {
                    if (!$isDeleteRole) {
                        if ($isAddRole) {
                            $roles = $userGranted->getRoles();
                        }
                        $roles[] = $role;
                    } else {
                        $roles = [];
                    }
                    $userGranted->setRoles($roles);
                }
                $em = $this->getDoctrine()->getManager();
                $em->flush();
            }
            return $this->render("APMCoreBundle:staff/superAdmin:user-role-show.html.twig", ['usersGranted' => $utilisateurs]);
        }

        return $this->render("APMCoreBundle:staff/superAdmin:user-role-edit.html.twig", ['edit_form' => $form->createView(),

            ]

        );
    }

    public function grantRoleShowAction(Utilisateur $utilisateur)
    {

        return $this->render("APMCoreBundle:staff/superAdmin:user-role-show.html.twig", ['userGranted' => $utilisateur]

        );
    }
}