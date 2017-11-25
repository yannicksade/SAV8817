<?php

namespace APM\AdminBundle\Controller;

use APM\UserBundle\Entity\Utilisateur_avm;
use APM\UserBundle\Form\Type\Utilisateur_avmType;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class UtilisateurAvmController
 * @RouteResource("user", pluralize=false)
 */
class UtilisateurAvmController extends Controller
{
    //retourne un entity manager
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Get("/users", name="s")
     */
    public function getAction(Request $request)
    {
        $user = new Utilisateur_avm();
        $form = $this->createForm(Utilisateur_avmType::class, $user);

        $em = $this->getEM();
        $form->handleRequest($request);
        // $request = $this->getRequest();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('apm_utilisateur_index'));
        }

        $allUsersAvm = $this->getEM()->getRepository('APMUserBundle:Utilisateur_avm')->findAll();

        return $this->render('APMAdminBundle:Utilisateur_AVM:index_old.html.twig', [
            'users' => $allUsersAvm,
            'form' => $form->createView()
        ]);
    }

    public function getEM()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @param Utilisateur_avm $user
     * @param $state
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Put("/user/{id}/enable/{state}")
     */
    public function enableAction(Utilisateur_avm $user, $state)
    {
        $em = $this->getEM();
        $state = boolval($state);
        $user->setEnabled($state);
        $em->merge($user);
        $em->flush();

        return $this->redirect($this->generateUrl('apm_utilisateur_index'));
    }

    /**
     * @param Utilisateur_avm $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @delete("/delete/user/{id}")
     */
    public function deleteAction(Utilisateur_avm $user) {
        $em = $this->getEM();
        try {
            $em->remove($user);
            $em->flush();
        } catch(ConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->Add('danger', "Vous ne pouvez pas supprimer cet utilisateur!");
            return $this->redirect($this->generateUrl('apm_utilisateur_index'));
        }
        $this->get('session')->getFlashBag()->Add('success', "Utilisateur supprimé avec succès!");
        return $this->redirect($this->generateUrl('apm_utilisateur_index'));
    }

    /**
     * @Patch("/edit/user/{id}")
     * @param Utilisateur_avm $utilisateur_avm
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Utilisateur_avm $utilisateur_avm, Request $request)
    {
        //$request = $this->get('request');
        $em = $this->getEM();
        $form = $this->createForm(RegistrationFormType::class, $utilisateur_avm);
        $form->handleRequest($request);
        //$request->getMethod() == 'POST'
        if ($form->isSubmitted() && $form->isValid()) {

            $em->merge($utilisateur_avm);
            $em->flush();
            $this->get('session')->getFlashBag()->Add('success', "Cet utilisateur a été modifié avec succès!");
            return $this->redirect($this->generateUrl('apm_admin_index'));
        }

        return $this->render('APMAdminBundle:Administrateur:update.html.twig', [
            'form' => $form->createView(),
            'admin' => $utilisateur_avm
        ]);
    }
}
