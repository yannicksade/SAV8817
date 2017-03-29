<?php

namespace APM\AdminBundle\Controller;

use APM\UserBundle\Entity\Admin;
use APM\UserBundle\Form\Type\RegistrationAdminFormType;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller {

    //retourne un entity manager
    public function indexAction(Request $request)
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

        return $this->render('APMAdminBundle:Administrateur:index.html.twig', [
            'form' => $form->createView(),
            'admins' => $allAdmins
        ]);
    }

    public function getEM()
    {
        return $this->getDoctrine()->getManager();
    }

    public function AbleOrEnableAction(Admin $admin, $val) {
        $em = $this->getEM();

        $admin->setEnabled($val);
        $em->merge($admin);
        $em->flush();

        return $this->redirect($this->generateUrl('apm_admin_index'));
    }

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

    public function updateAction(Admin $admin, Request $request)
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
