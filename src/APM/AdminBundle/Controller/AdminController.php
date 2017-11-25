<?php

namespace APM\AdminBundle\Controller;

use APM\UserBundle\Entity\Admin;
use APM\UserBundle\Form\Type\RegistrationAdminFormType;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class AdminController
 * @RouteResource("staff", pluralize=false)
 */
class AdminController extends FOSRestController
{

    //retourne un entity manager
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Get("/staffs", name="s")
     */
    public function getAction(Request $request)
    {
        $admin = new Admin();
        $form = $this->createForm(RegistrationAdminFormType::class, $admin);
        $em = $this->getEM();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($admin);
            $em->flush();
            return $this->redirect($this->generateUrl('apm_admin_index'));
        }

        $allAdmins = $em->getRepository('APMUserBundle:Admin')->findAll();

        return $this->render('APMAdminBundle:Administrateur:index_old.html.twig', [
            'form' => $form->createView(),
            'admins' => $allAdmins,
        ]);
    }

    private function getEM()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @param Admin $admin
     * @param $state
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Put("/staff/{id}/enable/{state}")
     */
    public function enableAction(Admin $admin, $state)
    {
        $em = $this->getEM();
        $state = boolval($state);
        $admin->setEnabled($state);
        $em->merge($admin);
        $em->flush();

        return $this->redirect($this->generateUrl('apm_admin_index'));
    }

    /**
     * @param Admin $admin
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @delete("/delete/staff/{id}")
     */
    public function deleteAction(Admin $admin) {
        $em = $this->getEM();
        try {
            $em->remove($admin);
            $em->flush();
        } catch(ConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->Add('danger', "Vous ne pouvez pas supprimer cet Administrateur!");
            return $this->redirect($this->generateUrl('apm_admin_index'));
        }
        $this->get('session')->getFlashBag()->Add('success', "Administrateur supprimé avec succès!");
        return $this->redirect($this->generateUrl('apm_admin_index'));
    }

    /**
     * @param Admin $admin
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Patch("/edit/staff/{id}")
     */
    public function editAction(Admin $admin, Request $request)
    {
        //$request = $this->get('request');
        $em = $this->getEM();
        $form = $this->createForm(RegistrationAdminFormType::class, $admin);
        $form->handleRequest($request);
        //$request->getMethod() == 'POST'
        if ($form->isSubmitted() && $form->isValid()) {

            $em->merge($admin);
            $em->flush();
            $this->get('session')->getFlashBag()->Add('success', "Cet administrateur a été modifié avec succès!");
            return $this->redirect($this->generateUrl('apm_admin_index'));
        }

        return $this->render('APMAdminBundle:Administrateur:update.html.twig', [
            'form' => $form->createView(),
            'admin' => $admin
        ]);
    }
}
