<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Groupe_offre;
use APM\AchatBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Groupe_offre controller.
 * Liste les Groupe d'offre crees par l'utilisateur
 */
class Groupe_offreController extends Controller
{
    /**
     * @param Request $request
     * @param Groupe_offre $groupe_offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, Groupe_offre $groupe_offre = null)
    {
        $this->listAndShowSecurity();
        /** @var Session $session */
        $session = $this->get('session');
        if (null === $groupe_offre) {//create
            $this->createSecurity();
            /** @var Groupe_offre $groupe_offre */
            if (!$groupe_offre) $groupe_offre = TradeFactory::getTradeProvider("groupe_offre");
        } else { //edit
            $this->editAndDeleteSecurity($groupe_offre);
        }
        $form = $this->createForm('APM\AchatBundle\Form\Groupe_offreType', $groupe_offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { //save
            $this->createSecurity();
            $groupe_offre->setCreateur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            try {
                $em->persist($groupe_offre);
                $em->flush();
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "Echec de l'enregistrement: " . "<strong>" . $groupe_offre . "</strong><br>La création ou la modification du groupe d'offre a échoué!");
                $groupe_offre = null;
                return $this->redirectToRoute('apm_achat_groupe_index');
            }
            $session->getFlashBag()->add('success', "Enregistrement du groupe d'offres: " . "<strong>" . $groupe_offre . "</strong><br>Opération effectuée avec succès!");
            $groupe_offre = null;
            return $this->redirectToRoute('apm_achat_groupe_index');
        }
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $groupe_offres = $user->getGroupesOffres();//liste
        return $this->render('APMAchatBundle:groupe_offre:index.html.twig', array(
            'groupe_offres' => $groupe_offres,
            'form' => $form->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    private function listAndShowSecurity(Groupe_offre $groupe_offre = null)
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        if ($groupe_offre !== null) {
            if ($this->getUser() !== $groupe_offre->getCreateur()) {
                throw $this->createAccessDeniedException();
            }
        }
        //------------------------------------------------------------------------------
    }

    private function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        /* ensure that the user is logged in
        */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Groupe_offre $groupe_offre
     */
    private function editAndDeleteSecurity($groupe_offre)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($groupe_offre) {
            $user = $this->getUser();
            if ($groupe_offre->getCreateur() !== $user) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------

    }

    public function listeOffresGroupeAction(Groupe_offre $groupe_offre)
    {
        $offres = $groupe_offre->getOffres();
        return $this->render('APMVenteBundle:offre:index.html.twig', array(
            'offres' => $offres,
            'boutique' => null,
            'categorie' => null,
            'vendeur' => null,
        ));
    }

    public function deleteAction(Groupe_offre $groupe_offre)
    {
        /** @var Session $session */
        $session = $this->get('session');
        $this->editAndDeleteSecurity($groupe_offre);
        $em = $this->getDoctrine()->getManager();
        try {
            $em->remove($groupe_offre);
            $em->flush();
        } catch (ConstraintViolationException $cve) {
            $session->getFlashBag()->add('danger', "Echec de la suppression de: " . "<strong>" . $groupe_offre . "</strong><br>L'opération de suppression du groupe d'offre a échoué!");
            return $this->redirectToRoute('apm_achat_groupe_index');
        }
        $session->getFlashBag()->add('success', "Suppression du groupe d'offres: " . "<strong>" . $groupe_offre . "</strong><br>Opération effectuée avec succès!");
        return $this->redirectToRoute('apm_achat_groupe_index');
    }
}
