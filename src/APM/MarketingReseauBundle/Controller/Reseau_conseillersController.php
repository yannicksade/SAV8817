<?php

namespace APM\MarketingReseauBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Reseau_conseillers controller.
 * Liste le réseau du conseiller
 */
class Reseau_conseillersController extends Controller
{
    // liste les réseaux
    public function indexAction(Conseiller $conseiller = null)
    {
        $this->listAndShowSecurity();
        if (!$conseiller) {
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $conseiller = $user->getProfileConseiller();
        }
        return $this->render('APMMarketingReseauBundle:reseau_conseillers:index_old.html.twig', array(
            'conseiller' => $conseiller,
        ));
    }

    /**
     */
    private function listAndShowSecurity()
    {
        //---------------------------------security-----------------------------------------------

        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        //----------------------------------------------------------------------------------------
    }

    public function addRightMemberAction(Request $request, Conseiller $conseiller)
    {
        /** @var Conseiller $conseiller */
        $this->addSecurity($conseiller);
        $oldRightAdvisor = $conseiller->getConseillerDroite();
        /** @var Utilisateur_avm $user */
        $form = $this->createForm('APM\MarketingReseauBundle\Form\Reseau_conseillersType');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Conseiller $advisorRight */
            $advisorRight = $form->get('conseiller')->getData();
            $this->addSecurity($conseiller, $advisorRight);
            $em = $this->getEM();
            $remplacer = $form->get('remplacer')->getData();
            if ($oldRightAdvisor !== $advisorRight) {
                if (null !== $advisorRight) {//gérer l'ajout ou l'insertion d'un conseiller à gauche
                    if (null !== $oldRightAdvisor) {
                        if (null !== $oldRightAdvisor->getUtilisateur()) {
                            if ($remplacer) {
                                $oldRightAdvisorChild_left = $oldRightAdvisor->getConseillerGauche();
                                $oldRightAdvisorChild_right = $oldRightAdvisor->getConseillerDroite();
                                $oldRightAdvisor->setConseillerGauche(null);
                                $oldRightAdvisor->setConseillerDroite(null);
                                $oldRightAdvisor->setMasterConseiller(null);
                                $advisorRight->setConseillerGauche($oldRightAdvisorChild_left);
                                $advisorRight->setConseillerDroite($oldRightAdvisorChild_right);
                                if ($oldRightAdvisorChild_right) $oldRightAdvisorChild_right->setMasterConseiller($advisorRight);
                                if ($oldRightAdvisorChild_left) $oldRightAdvisorChild_left->setMasterConseiller($advisorRight);
                            } else {
                                $oldRightAdvisor->setMasterConseiller($advisorRight);
                                $advisorRight->setConseillerGauche($oldRightAdvisor);
                            }
                        } else {
                            $oldRightAdvisorChild_left = $oldRightAdvisor->getConseillerGauche();
                            $oldRightAdvisorChild_right = $oldRightAdvisor->getConseillerDroite();
                            $oldRightAdvisor->setConseillerGauche(null);
                            $oldRightAdvisor->setConseillerDroite(null);
                            $oldRightAdvisor->setMasterConseiller(null);
                            $oldRightAdvisor->setNombreInstanceReseau(0);
                            $advisorRight->setConseillerGauche($oldRightAdvisorChild_left);
                            $advisorRight->setConseillerDroite($oldRightAdvisorChild_right);
                            if ($oldRightAdvisorChild_right) $oldRightAdvisorChild_right->setMasterConseiller($advisorRight);
                            if ($oldRightAdvisorChild_left) $oldRightAdvisorChild_left->setMasterConseiller($advisorRight);
                        }
                    }
                    $advisorRight->setMasterConseiller($conseiller);
                    if ($conseiller) $conseiller->setConseillerDroite($advisorRight);
                    $em->flush();
                }//gérer le départ d'un membre de gauche
                elseif (null !== $oldRightAdvisor && null !== $oldRightAdvisor->getUtilisateur()) {//s'il yavait un conseiller à cette position, le lier au master conseiller supérieur
                    $oldLeftAdvisorChild = $oldRightAdvisor->getConseillerGauche();
                    $oldRightAdvisorChild = $oldRightAdvisor->getConseillerDroite();
                    if (!$oldLeftAdvisorChild && !$oldRightAdvisorChild) {
                        $conseillerFictifDroite = null;
                    } else {
                        $conseillerFictifDroite = $em->getRepository('APMMarketingDistribueBundle:Conseiller')
                            ->findOneBy(['masterConseiller' => null, 'utilisateur' => null]);
                        if (null === $conseillerFictifDroite) {
                            /** @var Conseiller $conseillerFictifDroite */
                            $conseillerFictifDroite = TradeFactory::getTradeProvider('conseiller');
                            if (null !== $conseillerFictifDroite) {
                                $em->persist($conseillerFictifDroite);
                                $em->flush();
                            }
                        }
                    }
                    if ($conseillerFictifDroite) $conseillerFictifDroite->setMasterConseiller($conseiller);
                    if (null !== $oldLeftAdvisorChild) {
                        $oldRightAdvisor->setConseillerGauche(null);
                        $oldLeftAdvisorChild->setMasterConseiller($conseillerFictifDroite);
                        if ($conseillerFictifDroite) $conseillerFictifDroite->setConseillerGauche($oldLeftAdvisorChild);
                    }
                    if (null !== $oldRightAdvisorChild) {
                        $oldRightAdvisor->setConseillerDroite(null);
                        if ($oldRightAdvisorChild) $oldRightAdvisorChild->setMasterConseiller($conseillerFictifDroite);
                        if ($conseillerFictifDroite) $conseillerFictifDroite->setConseillerDroite($oldRightAdvisorChild);
                    }
                    $oldRightAdvisor->setMasterConseiller(null);

                    if ($conseiller) $conseiller->setConseillerDroite($conseillerFictifDroite); //Fils du conseiller de droite sortant
                    $em->flush();
                }

                return $this->redirectToRoute('apm_marketing_reseau_show', array('id' => $conseiller->getId()));
            }
        }
        return $this->render('APMMarketingReseauBundle:reseau_conseillers:new.html.twig', array(
            'conseiller' => $conseiller,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Conseiller $advisor
     * @param Conseiller $member
     */
    private function addSecurity($advisor, $member = null)
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
        if (null === $conseiller || $conseiller !== $advisor || !($conseiller->getMasterConseiller() instanceof Conseiller)) {
            throw $this->createAccessDeniedException();
        }
        if (null !== $member && null !== $member->getMasterConseiller())
            throw $this->createAccessDeniedException();

        //----------------------------------------------------------------------------------------
    }

    private function getEM()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @param Conseiller $advisorLeft
     * @param Conseiller $advisorRight
     */
    /*
private function LocateGrandMaster($advisorLeft, $advisorRight)
{
 //--------------- remonter un réseau jusqu'au SuperMaster ---------------------
 $master = $advisorLeft;
 $grandMaster = $master->getMasterConseiller();
 while(null !== $master && $master !== $grandMaster){
     $master = $grandMaster;
     $grandMaster = $master->getMasterConseiller();
 }
 $grandMasterLeft = $grandMaster;

 $master = $advisorRight;
 $grandMaster = $master->getMasterConseiller();
 while(null !== $master && $master !== $grandMaster){
     $master = $grandMaster;
     $grandMaster = $master->getMasterConseiller();
 }
 $grandMasterRight = $grandMaster;
 //---------------------------------------------------------------------------------

    }
*/

    /**
     * Ajoute ou modifie le conseiller de droite ou de gauche
     * @param Request $request
     * @param Conseiller $conseiller
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addLeftMemberAction(Request $request, Conseiller $conseiller)
    {
        $this->addSecurity($conseiller);
        $oldLeftAdvisor = $conseiller->getConseillerGauche();
        /** @var Utilisateur_avm $user */
        $form = $this->createForm('APM\MarketingReseauBundle\Form\Reseau_conseillersType');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Conseiller $advisorLeft */
            $advisorLeft = $form->get('conseiller')->getData();
            $this->addSecurity($conseiller, $advisorLeft);
            $em = $this->getDoctrine()->getManager();
            $remplacer = $form->get('remplacer')->getData();
            if ($oldLeftAdvisor !== $advisorLeft) {//changement d'un membre
                if (null !== $advisorLeft) {//gérer l'ajout ou l'insertion d'un conseiller à gauche
                    if (null !== $oldLeftAdvisor) {
                        if (null !== $oldLeftAdvisor->getUtilisateur()) {
                            if ($remplacer) {//remplacement
                                $oldLeftAdvisorChild_left = $oldLeftAdvisor->getConseillerGauche();
                                $oldLeftAdvisorChild_right = $oldLeftAdvisor->getConseillerDroite();
                                $oldLeftAdvisor->setConseillerGauche(null);
                                $oldLeftAdvisor->setConseillerDroite(null);
                                $oldLeftAdvisor->setMasterConseiller(null);
                                $advisorLeft->setConseillerGauche($oldLeftAdvisorChild_left);
                                $advisorLeft->setConseillerDroite($oldLeftAdvisorChild_right);
                                if ($oldLeftAdvisorChild_right) $oldLeftAdvisorChild_right->setMasterConseiller($advisorLeft);
                                if ($oldLeftAdvisorChild_left) $oldLeftAdvisorChild_left->setMasterConseiller($advisorLeft);
                            } else { //insertion
                                $oldLeftAdvisor->setMasterConseiller($advisorLeft);
                                $advisorLeft->setConseillerGauche($oldLeftAdvisor);

                            }
                        } else {//remplacement d'un conseiller fictif
                            $oldLeftAdvisorChild_left = $oldLeftAdvisor->getConseillerGauche();
                            $oldLeftAdvisorChild_right = $oldLeftAdvisor->getConseillerDroite();
                            $oldLeftAdvisor->setConseillerGauche(null);
                            $oldLeftAdvisor->setConseillerDroite(null);
                            $oldLeftAdvisor->setMasterConseiller(null);
                            $oldLeftAdvisor->setNombreInstanceReseau(0);
                            $advisorLeft->setConseillerGauche($oldLeftAdvisorChild_left);
                            $advisorLeft->setConseillerDroite($oldLeftAdvisorChild_right);
                            if ($oldLeftAdvisorChild_right) $oldLeftAdvisorChild_right->setMasterConseiller($advisorLeft);
                            if ($oldLeftAdvisorChild_left) $oldLeftAdvisorChild_left->setMasterConseiller($advisorLeft);
                        }
                    }
                    $advisorLeft->setMasterConseiller($conseiller);
                    if ($conseiller) $conseiller->setConseillerGauche($advisorLeft);

                    $em->flush();

                } //gérer le départ d'un membre de gauche
                elseif (null !== $oldLeftAdvisor && null !== $oldLeftAdvisor->getUtilisateur()) {//s'il yavait un conseiller à cette position, le lier au maître conseiller supérieur
                    $oldLeftAdvisorChild = $oldLeftAdvisor->getConseillerGauche();
                    $oldRightAdvisorChild = $oldLeftAdvisor->getConseillerDroite();
                    if (!$oldLeftAdvisorChild && !$oldRightAdvisorChild) {
                        $conseillerFictifGauche = null;
                    } else {
                        $conseillerFictifGauche = $em->getRepository('APMMarketingDistribueBundle:Conseiller')
                            ->findOneBy(['masterConseiller' => null, 'utilisateur' => null]);
                        if (null === $conseillerFictifGauche) {
                            /** @var Conseiller $conseillerFictifGauche */
                            $conseillerFictifGauche = TradeFactory::getTradeProvider('conseiller');
                            if ($conseillerFictifGauche) {
                                $em->persist($conseillerFictifGauche);
                                $em->flush();
                            }
                        }
                    }
                    if ($conseillerFictifGauche) $conseillerFictifGauche->setMasterConseiller($conseiller);
                    $conseiller->setConseillerGauche($conseillerFictifGauche);
                    if (null !== $oldLeftAdvisorChild) {
                        $oldLeftAdvisor->setConseillerGauche(null);
                        $oldLeftAdvisorChild->setMasterConseiller($conseillerFictifGauche);
                        if ($conseillerFictifGauche) $conseillerFictifGauche->setConseillerGauche($oldLeftAdvisorChild);
                    }

                    if (null !== $oldRightAdvisorChild) {
                        $oldLeftAdvisor->setConseillerDroite(null);
                        $oldRightAdvisorChild->setMasterConseiller($conseillerFictifGauche);
                        if ($conseillerFictifGauche) $conseillerFictifGauche->setConseillerDroite($oldRightAdvisorChild);
                    }
                    $oldLeftAdvisor->setMasterConseiller(null);

                    $em->flush();
                }


                return $this->redirectToRoute('apm_marketing_reseau_show', array('id' => $conseiller->getId()));
            }
        }
        return $this->render('APMMarketingReseauBundle:reseau_conseillers:new.html.twig', array(
            'conseiller' => $conseiller,
            'form' => $form->createView(),
        ));
    }

    public function PromoteLeftMemberAction(Conseiller $advisor)
    {
        $this->promotionSecurity($advisor);
        if (null !== $advisor) {
            $em = $this->getDoctrine()->getManager();
            $conseiller = $advisor->getMasterConseiller();
            if (null !== $conseiller) {
                $leftChild = $advisor->getConseillerGauche();
                $rightChild = $advisor->getConseillerDroite();
                $advisor->setConseillerDroite(null);
                $advisor->setConseillerGauche(null);
                $em->flush();
                if (null !== $leftChild) {
                    $member = $conseiller->getConseillerDroite();
                    if ($member === $advisor) {
                        $conseiller->setConseillerDroite($leftChild);
                    } else {
                        $conseiller->setConseillerGauche($leftChild);
                    }
                    $leftChildLeftGrandSon = $leftChild->getConseillerGauche();
                    $leftChildRightGrandSon = $leftChild->getConseillerDroite();
                    if ($leftChildRightGrandSon) $leftChildRightGrandSon->setMasterConseiller($advisor);
                    $advisor->setConseillerDroite($leftChildRightGrandSon);
                    if ($leftChildLeftGrandSon) $leftChildLeftGrandSon->setMasterConseiller($advisor);
                    $advisor->setConseillerGauche($leftChildLeftGrandSon);
                    if ($rightChild) $rightChild->setMasterConseiller($leftChild);
                    $leftChild->setConseillerDroite($rightChild);
                    if (!$leftChildRightGrandSon && !$leftChildLeftGrandSon) {
                        $advisor->setMasterConseiller(null);
                        $leftChild->setConseillerGauche(null);
                    } else {
                        $advisor->setMasterConseiller($leftChild);
                        $leftChild->setConseillerGauche($advisor);
                    }
                    $leftChild->setMasterConseiller($conseiller);


                    $em->flush();
                }
            }
        }
        return $this->redirectToRoute('apm_marketing_reseau_index', array('id' => $conseiller->getId()));
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
        if (null !== $conseiller && null !== $conseiller->getUtilisateur() || $conseillerCourant !== $conseiller->getMasterConseiller()) {
            throw $this->createAccessDeniedException();
        }
    }

    public function PromoteRightMemberAction(Conseiller $advisor)
    {

        $this->promotionSecurity($advisor);
        if (null !== $advisor) {
            $conseiller = $advisor->getMasterConseiller();
            $em = $this->getDoctrine()->getManager();
            if (null !== $conseiller) {
                $rightChild = $advisor->getConseillerDroite();
                $leftChild = $advisor->getConseillerGauche();
                $advisor->setConseillerDroite(null);
                $advisor->setConseillerGauche(null);
                $em->flush();
                $member = $conseiller->getConseillerGauche();
                if (null !== $rightChild) {
                    if ($member === $advisor) {
                        $conseiller->setConseillerGauche($rightChild);
                    } else {
                        $conseiller->setConseillerDroite($rightChild);
                    }
                    $rightChildLeftGrandSon = $rightChild->getConseillerGauche();
                    $rightChildRightGrandSon = $rightChild->getConseillerDroite();
                    if ($rightChildRightGrandSon) $rightChildRightGrandSon->setMasterConseiller($advisor);
                    $advisor->setConseillerDroite($rightChildRightGrandSon);
                    if ($rightChildLeftGrandSon) $rightChildLeftGrandSon->setMasterConseiller($advisor);
                    $advisor->setConseillerGauche($rightChildLeftGrandSon);
                    if ($leftChild) $leftChild->setMasterConseiller($rightChild);

                    $rightChild->setConseillerGauche($leftChild);
                    if (!$rightChildLeftGrandSon && !$rightChildRightGrandSon) {
                        $advisor->setMasterConseiller(null);
                        $rightChild->setConseillerDroite(null);
                    } else {
                        $advisor->setMasterConseiller($rightChild);
                        $rightChild->setConseillerDroite($advisor);
                    }
                    $rightChild->setMasterConseiller($conseiller);

                    $em->flush();
                }
            }
        }
        return $this->redirectToRoute('apm_marketing_reseau_index', array('id' => $conseiller->getId()));
    }

    /**
     * Activer son profile de manager de réseau conseiller
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $this->createSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $conseiller = $user->getProfileConseiller();
        if ($conseiller) {
            $conseiller->setMasterConseiller($conseiller);
            $conseiller->setNombreInstanceReseau(1);
            $em = $this->getEM();
            $em->flush();
        }
        return $this->redirectToRoute('apm_marketing_reseau_show', array('id' => $conseiller->getId()));
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
        if (null === $conseiller || !($conseiller->isConseillerA2()) || ($conseiller->getNombreInstanceReseau()) >= 1) {
            throw $this->createAccessDeniedException();
        }

        //----------------------------------------------------------------------------------------
    }

    public function showAction(Conseiller $conseiller)
    {
        $this->listAndShowSecurity();

        return $this->render('APMMarketingReseauBundle:reseau_conseillers:show.html.twig', array(
            'conseiller' => $conseiller,
        ));
    }
}
