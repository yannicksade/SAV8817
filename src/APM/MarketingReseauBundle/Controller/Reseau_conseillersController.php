<?php

namespace APM\MarketingReseauBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
            return $this->json(json_encode($json), 200);
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
     * @param Conseiller $conseiller
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addMemberAction(Request $request, Conseiller $conseiller)
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
            $this->addSecurity($conseiller, $advisor);
            $em = $this->getDoctrine()->getManager();
            if ($selfConseiller !== $conseiller) {
                if ($conseiller->getConseillerGauche() === $selfConseiller) {
                    $oldAdvisor = $conseiller->getConseillerGauche();
                    $position = 1;
                } else {
                    $oldAdvisor = $conseiller->getConseillerDroite();
                    $position = 0;
                }
            } else {
                $oldAdvisor = ($position = $form->get('position')->getData()) ? $conseiller->getConseillerGauche() : $conseiller->getConseillerDroite();
            }
            if ($oldAdvisor !== $advisor) {//changement d'un membre
                if (null !== $advisor) {//gérer l'ajout ou l'insertion d'un conseiller
                    if (null !== $oldAdvisor) {
                        if (null !== $oldAdvisor->getUtilisateur()) { //distinction d'un conseiller fictif
                            $remplacer = $form->get('modification')->getData();
                            if ($remplacer) {//remplacement d'un conseiller
                                $oldAdvisorChild_left = $oldAdvisor->getConseillerGauche();
                                $oldAdvisorChild_right = $oldAdvisor->getConseillerDroite();
                                $oldAdvisor->setConseillerGauche(null);
                                $oldAdvisor->setConseillerDroite(null);
                                $oldAdvisor->setMasterConseiller(null);
                                $advisor->setConseillerGauche($oldAdvisorChild_left);
                                $advisor->setConseillerDroite($oldAdvisorChild_right);
                                if ($oldAdvisorChild_right) $oldAdvisorChild_right->setMasterConseiller($advisor);
                                if ($oldAdvisorChild_left) $oldAdvisorChild_left->setMasterConseiller($advisor);
                            } else { //insertion
                                $oldAdvisor->setMasterConseiller($advisor);
                                $advisor->setConseillerGauche($oldAdvisor);
                            }
                        } else {//remplacement d'un conseiller fictif
                            $oldAdvisorChild_left = $oldAdvisor->getConseillerGauche();
                            $oldAdvisorChild_right = $oldAdvisor->getConseillerDroite();
                            $oldAdvisor->setConseillerGauche(null);
                            $oldAdvisor->setConseillerDroite(null);
                            $oldAdvisor->setMasterConseiller(null);
                            $oldAdvisor->setNombreInstanceReseau(0);
                            $advisor->setConseillerGauche($oldAdvisorChild_left);
                            $advisor->setConseillerDroite($oldAdvisorChild_right);
                            if ($oldAdvisorChild_right) $oldAdvisorChild_right->setMasterConseiller($advisor);
                            if ($oldAdvisorChild_left) $oldAdvisorChild_left->setMasterConseiller($advisor);
                        }
                    }
                    $advisor->setMasterConseiller($conseiller);
                    if ($conseiller) if ($position) $conseiller->setConseillerGauche($advisor); else $conseiller->setConseillerDroite($advisor);

                    $em->flush();

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
                                $em->flush();
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

                    $em->flush();
                }
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
     */
    private function addSecurity($master, $member = null)
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
        return $this->redirectToRoute('apm_marketing_reseau_index');
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
        return $this->redirectToRoute('apm_marketing_reseau_index');
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
