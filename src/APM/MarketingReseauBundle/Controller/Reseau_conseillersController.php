<?php

namespace APM\MarketingReseauBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reseau_conseillers controller.
 * Liste le réseau du conseiller
 */
class Reseau_conseillersController extends Controller
{
    /** Liste les binômes du réseau du conseiller (2 par 2) pour dessiner l'arbre
     * @param Request $request
     * @param Conseiller|null $conseiller
     * @return \Symfony\Component\HttpFoundation\Response| JsonResponse
     */
    public function indexAction(Request $request, Conseiller $conseiller = null)
    {
        $this->listAndShowSecurity($conseiller);
        /** @var Utilisateur_avm $user */
        if (null === $conseiller) {
            $user = $this->getUser();
            $conseiller = $user->getProfileConseiller();
        }
        if ($request->isXmlHttpRequest()) {
            $reseau = array();
            $reseau['gauche'] = array();
            $reseau['droite'] = array();
            $leftChild = $conseiller->getConseillerGauche();
            if (null !== $leftChild) {
                $reseau['gauche'] = array(
                    'id' => $leftChild->getId(),
                    'code' => $leftChild->getCode(),
                );
            }
            $rightChild = $conseiller->getConseillerDroite();
            if (null !== $rightChild) {
                $reseau['droite'] = array(
                    'id' => $rightChild->getId(),
                    'code' => $rightChild->getCode(),
                );
            }
            return $this->json(json_encode($reseau), 200);
        }

        return $this->render('APMMarketingReseauBundle:reseau_conseillers:index.html.twig', array(
            'conseiller' => $conseiller,
        ));
    }

    /**
     * @param Conseiller $conseiller
     */
    private function listAndShowSecurity($conseiller)
    {
        //---------------------------------security-----------------------------------------------

        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
        /** @var Conseiller $selfConseiller */
        $selfConseiller = $this->getUser()->getProfileConseiller();
        $master = null !== $selfConseiller ? $selfConseiller->getMasterConseiller() : null;
        if (null === $selfConseiller || $master === $conseiller) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * Ajoute ou modifie le conseiller de droite ou de gauche
     * @param Request $request
     * @param Conseiller $conseiller maître du reseau courant
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function handleNetworkingMembersAction(Request $request, Conseiller $conseiller)
    {
        $this->addSecurity($conseiller);
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $selfConseiller = $user->getProfileConseiller();
        $form = $this->createForm('APM\MarketingReseauBundle\Form\Reseau_conseillersType');
        if ($selfConseiller !== $conseiller) {
            $form->remove('conseiller');
            $form->remove('modification');
            $form->remove('position');
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Conseiller $advisor */
            $advisor = ($selfConseiller !== $conseiller) ? null : $form->get('conseiller')->getData();
            $modification = $form->has('modification') ? $form->get('modification')->getData() : 1;
            $this->addSecurity($conseiller, $advisor, $modification);
            $em = $this->getDoctrine()->getManager();
            if ($selfConseiller !== $conseiller) { //cas: quitter le réseau du maitre
                if ($conseiller->getConseillerGauche() === $selfConseiller) {
                    $oldAdvisor = $conseiller->getConseillerGauche();
                    $position = true;
                } else {
                    $oldAdvisor = $conseiller->getConseillerDroite();
                    $position = false;
                }
            } else {
                $position = boolval($form->get('position')->getData());
                $oldAdvisor = ($position) ? $conseiller->getConseillerGauche() : $conseiller->getConseillerDroite();
            }
            if ($oldAdvisor !== $advisor) {//changement d'un membre
                if (null !== $advisor) {//gérer l'ajout, l'insertion d'un conseiller ou la fussion d'une branche
                    if (null !== $oldAdvisor) {
                       // if (null !== $oldAdvisor->getUtilisateur()) { //distinction d'un conseiller fictif
                            switch ($modification) {
                                case 0 ://inserer un membre
                                    $oldAdvisor->setMasterConseiller($advisor);
                                    $advisor->setConseillerGauche($oldAdvisor);
                                    break;
                                case 1 : //remplacer un membre
                                    $oldAdvisorChild_left = $oldAdvisor->getConseillerGauche();
                                    $oldAdvisorChild_right = $oldAdvisor->getConseillerDroite();
                                    $oldAdvisor->setConseillerGauche(null);
                                    $oldAdvisor->setConseillerDroite(null);
                                    $oldAdvisor->setMasterConseiller(null);
                                    $advisor->setConseillerGauche($oldAdvisorChild_left);
                                    $advisor->setConseillerDroite($oldAdvisorChild_right);
                                    if ($oldAdvisorChild_right) $oldAdvisorChild_right->setMasterConseiller($advisor);
                                    if ($oldAdvisorChild_left) $oldAdvisorChild_left->setMasterConseiller($advisor);
                                    break;
                                case 2 : //fusionner une branche
                                    $master = $conseiller->getMasterConseiller();
                                    if($conseiller !== $master) $master->getConseillerGauche() === $conseiller ? $master->setConseillerGauche(null) : $master->setConseillerDroite(null); //détachement d'une branche
                                    if ($position) {
                                        $advisorLeftChild = $advisor->getConseillerGauche();
                                        $leftChild = $conseiller->getConseillerGauche();
                                        $conseillerLeftChild = $leftChild;
                                        while (null !== $leftChild) {
                                            $conseillerLeftChild = $leftChild;
                                            $leftChild = $leftChild->getConseillerGauche();
                                        }
                                        if($conseillerLeftChild)$conseillerLeftChild->setConseillerGauche($advisorLeftChild);
                                        if($advisorLeftChild)$advisorLeftChild->setMasterConseiller($conseillerLeftChild);
                                    } else {
                                        $advisorRightChild = $advisor->getConseillerDroite();
                                        $rightChild = $conseiller->getConseillerDroite();
                                        $conseillerRightChild = $rightChild;
                                        while (null !== $rightChild) {
                                            $conseillerRightChild = $rightChild;
                                            $rightChild = $rightChild->getConseillerDroite();
                                        }
                                        if($conseillerRightChild)$conseillerRightChild->setConseillerDroite($advisorRightChild);
                                        if($advisorRightChild)$advisorRightChild->setMasterConseiller($conseillerRightChild);
                                    }
                                    $temp = $conseiller;
                                    $conseiller = $advisor;
                                    $advisor = $temp;
                            }
                    }
                    $advisor->setMasterConseiller($conseiller);
                    if ($conseiller) if ($position) $conseiller->setConseillerGauche($advisor); else $conseiller->setConseillerDroite($advisor);

                } //gérer le départ d'un membre
                elseif (null !== $oldAdvisor && null !== $oldAdvisor->getUtilisateur()) {//s'il yavait un conseiller à cette position, le lier au maître conseiller supérieur
                    $oldAdvisorChild_left = $oldAdvisor->getConseillerGauche();
                    $oldAdvisorChild_right = $oldAdvisor->getConseillerDroite();
                    if (!$oldAdvisorChild_left && !$oldAdvisorChild_right) {
                        $conseillerFictif = null;
                    } else {
                        $conseillerFictif = $em->getRepository('APMMarketingDistribueBundle:Conseiller')
                            ->findOneBy(['masterConseiller' => null, 'utilisateur' => null]);
                        if (null === $conseillerFictif) {
                            /** @var Conseiller $conseillerFictif */
                            $conseillerFictif = TradeFactory::getTradeProvider('conseiller');
                            if ($conseillerFictif) {
                                $em->persist($conseillerFictif);
                                //$em->flush();
                            }
                        }
                    }
                    if ($conseillerFictif) $conseillerFictif->setMasterConseiller($conseiller);
                    if ($position) $conseiller->setConseillerGauche($conseillerFictif); else $conseiller->setConseillerDroite($conseillerFictif);
                    if (null !== $oldAdvisorChild_left) {
                        $oldAdvisor->setConseillerGauche(null);
                        $oldAdvisorChild_left->setMasterConseiller($conseillerFictif);
                        if ($conseillerFictif) $conseillerFictif->setConseillerGauche($oldAdvisorChild_left);
                    }

                    if (null !== $oldAdvisorChild_right) {
                        $oldAdvisor->setConseillerDroite(null);
                        $oldAdvisorChild_right->setMasterConseiller($conseillerFictif);
                        if ($conseillerFictif) $conseillerFictif->setConseillerDroite($oldAdvisorChild_right);
                    }
                    $oldAdvisor->setMasterConseiller(null);

                   // $em->flush();
                }
                $em->flush();
                $conseiller = $user->getProfileConseiller();
                if (null === $conseiller->getMasterConseiller()) {
                    $route = 'apm_marketing_conseiller_show';
                    $param = array('id' => $conseiller->getId());
                } else {
                    $route = 'apm_marketing_reseau_index';
                    $param = [];
                }
                return $this->redirectToRoute($route, $param);
            }
        }
        return $this->render('APMMarketingReseauBundle:reseau_conseillers:new.html.twig', array(
            'conseiller' => $conseiller,
            'form' => $form->createView(),
        ));
    }


    /**
     * @param Conseiller $master
     * @param Conseiller $member
     * @param int $modification
     */
    private function addSecurity($master, $member = null, $modification = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $conseiller = $user->getProfileConseiller();
        if (null === $conseiller || !$conseiller->getMasterConseiller() instanceof Conseiller || $conseiller !== $master && $conseiller !== $master->getConseillerDroite() && $conseiller !== $master->getConseillerGauche()
            || $conseiller !== $master && null !== $member
        ) {
            throw $this->createAccessDeniedException();
        }
        if (null !== $member && null !== $member->getMasterConseiller() && $modification != 2)
            throw $this->createAccessDeniedException();
        //----------------------------------------------------------------------------------------
    }

    private function getEM()
    {
        return $this->getDoctrine()->getManager();
    }


    /**
     * @param Request $request
     * @param Conseiller $advisorFictif
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse | Response
     */
    public function PromoteMemberAction(Request $request, Conseiller $advisorFictif)
    {
        $this->promotionSecurity($advisorFictif);
        $form = $this->createForm('APM\MarketingReseauBundle\Form\Reseau_conseillersType');
        $form->remove('conseiller');
        $form->remove('modification');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $position = boolval($form->get('position')->getData());
            if ($position) {
                $promotingChild = $advisorFictif->getConseillerGauche();
                $advisorFictif->setConseillerGauche(null);
                $siblingChild = $advisorFictif->getConseillerDroite();
                $advisorFictif->setConseillerDroite(null);
            } else {
                $promotingChild = $advisorFictif->getConseillerDroite();
                $advisorFictif->setConseillerDroite(null);
                $siblingChild = $advisorFictif->getConseillerGauche();
                $advisorFictif->setConseillerGauche(null);
            }
            $master = $advisorFictif->getMasterConseiller();
            if (null !== $promotingChild) {
                $member = $master->getConseillerGauche();
                if ($member === $advisorFictif) {
                    $master->setConseillerGauche($promotingChild);
                } else {
                    $master->setConseillerDroite($promotingChild);
                }
                $promotingChildLeftGrandSon = $promotingChild->getConseillerGauche();
                $promotingChild->setConseillerGauche(null);
                $promotingChildRightGrandSon = $promotingChild->getConseillerDroite();
                $promotingChild->setConseillerDroite(null);
                // le conseiller fictif devra supporter les fils du promotingChild qui change de position
                if ($promotingChildRightGrandSon) $promotingChildRightGrandSon->setMasterConseiller($advisorFictif);
                $advisorFictif->setConseillerDroite($promotingChildRightGrandSon);
                if ($promotingChildLeftGrandSon) $promotingChildLeftGrandSon->setMasterConseiller($advisorFictif);
                $advisorFictif->setConseillerGauche($promotingChildLeftGrandSon);

                if ($siblingChild) $siblingChild->setMasterConseiller($promotingChild);
                !$position ? $promotingChild->setConseillerGauche($siblingChild) : $promotingChild->setConseillerDroite($siblingChild);

                if (!$promotingChildLeftGrandSon && !$promotingChildRightGrandSon) {
                    $advisorFictif->setMasterConseiller(null);
                } else {
                    $advisorFictif->setMasterConseiller($promotingChild);
                    $position ? $promotingChild->setConseillerGauche($advisorFictif) : $promotingChild->setConseillerDroite($advisorFictif);
                }
                $promotingChild->setMasterConseiller($master);

                $em->flush();
            }
            return $this->redirectToRoute('apm_marketing_reseau_index');
        }
        return $this->render('APMMarketingReseauBundle:reseau_conseillers:new.html.twig', array(
            'conseiller' => null,
            'form' => $form->createView(),
        ));
    }


    /**
     * @param Conseiller $conseiller
     */
    private function promotionSecurity($conseiller)
    {
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $conseillerCourant = $user->getProfileConseiller();
        //s'assurer qu'il s'agit d'un conseiller fictif membre du conseiller courant
        if (null === $conseiller || null !== $conseiller && null !== $conseiller->getUtilisateur() || $conseillerCourant !== $conseiller->getMasterConseiller()) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * Activer son profile de manager de réseau conseiller
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response |JsonResponse
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $conseiller = $user->getProfileConseiller();
        $conseiller->setMasterConseiller($conseiller);
        $conseiller->setNombreInstanceReseau(1);
        $em = $this->getEM();
        $em->flush();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array();
            return $this->json(json_encode($json), 200);
        }
        return $this->redirectToRoute('apm_marketing_reseau_index');
    }

    private function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER_A2', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $conseiller = $user->getProfileConseiller();
        if (null === $conseiller || !($conseiller->isConseillerA2()) || ($conseiller->getNombreInstanceReseau()) >= 1 || null !== $conseiller->getMasterConseiller()) {
            throw $this->createAccessDeniedException();
        }

        //----------------------------------------------------------------------------------------
    }

}
