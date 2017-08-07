<?php

namespace APM\AdminBundle\Controller;

use APM\UserBundle\Entity\Utilisateur_avm;
use APM\UserBundle\Form\Type\Utilisateur_avmType;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UtilisateurAvmController extends Controller
{
    //retourne un entity manager
    public function indexAction(Request $request)
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

    public function AbleOrEnableAction(Utilisateur_avm $user, $val) {
        $em = $this->getEM();

        $user->setEnabled($val);
        $em->merge($user);
        $em->flush();

        return $this->redirect($this->generateUrl('apm_utilisateur_index'));
    }

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
}
