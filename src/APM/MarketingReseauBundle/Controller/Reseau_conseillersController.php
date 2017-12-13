<?php

namespace APM\MarketingReseauBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Reseau_conseillers controller.
 *
 * @RouteResource("network", pluralize=false)
 */
class Reseau_conseillersController extends Controller
{
    /** Liste les binômes du réseau du conseiller (2 par 2) pour dessiner l'arbre
     * @param Request $request
     * @param Conseiller|null $conseiller
     * @return JsonResponse
     *
     * @Get("/network")
     * @Put("/new-network/conseiller{id}", name="_conseiller")
     */
    public function getAction(Request $request, Conseiller $conseiller = null)
    {
        $this->listAndShowSecurity($conseiller);
        /** @var Utilisateur_avm $user */
        if (null === $conseiller) {
            $user = $this->getUser();
            $conseiller = $user->getProfileConseiller();
            if (null === $conseiller) return $this->json("not found", 404);
        }
        $data = $this->get('apm_core.data_serialized')->getFormalData($conseiller, array("net", "owner_list"));
        return new JsonResponse($data, 200);
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|JsonResponse
     *
     * @Post("/network/add-member/conseiller/{id}", name="_conseiller")
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
                $position = $form->get('position')->getData();
                $oldAdvisor = ($position) ? $conseiller->getConseillerGauche() : $conseiller->getConseillerDroite();
            }
            if ($oldAdvisor !== $advisor) {//changement d'un membre
                if (null !== $advisor) {//gérer l'ajout, l'insertion d'un conseiller ou la fusion d'une branche
                    switch ($modification) {
                        case 0 ://inserer un membre
                            if (null === $oldAdvisor) break;
                            $oldAdvisor->setMasterConseiller($advisor);
                            $advisor->setConseillerGauche($oldAdvisor);
                            break;
                        case 1 : //remplacer un membre
                            if (null === $oldAdvisor) break;
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
                            if ($conseiller !== $master) $master->getConseillerGauche() === $conseiller ? $master->setConseillerGauche(null) : $master->setConseillerDroite(null); //détachement d'une branche
                            if ($position) {
                                $advisorLeftChild = $advisor->getConseillerGauche();
                                $leftChild = $oldAdvisor;
                                $conseillerLeftChild = $conseiller;
                                while (null !== $leftChild) {
                                    $conseillerLeftChild = $leftChild;
                                    $leftChild = $leftChild->getConseillerGauche();
                                }
                                $conseillerLeftChild->setConseillerGauche($advisorLeftChild);
                                if ($advisorLeftChild) $advisorLeftChild->setMasterConseiller($conseillerLeftChild);
                            } else {
                                $advisorRightChild = $advisor->getConseillerDroite();
                                $rightChild = $oldAdvisor;
                                $conseillerRightChild = $conseiller;
                                while (null !== $rightChild) {
                                    $conseillerRightChild = $rightChild;
                                    $rightChild = $rightChild->getConseillerDroite();
                                }
                                $conseillerRightChild->setConseillerDroite($advisorRightChild);
                                if ($advisorRightChild) $advisorRightChild->setMasterConseiller($conseillerRightChild);
                            }
                            $temp = $conseiller;
                            $conseiller = $advisor;
                            $advisor = $temp;
                    }
                    $advisor->setMasterConseiller($conseiller);
                    if ($position) $conseiller->setConseillerGauche($advisor); else $conseiller->setConseillerDroite($advisor);

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
                    $oldAdvisor->setNombreInstanceReseau(0);
                }
                $em->flush();
                $conseiller = $user->getProfileConseiller();
                if($request->isXmlHttpRequest()){
                    $json = array();
                    $json['item'] = array();
                    return $this->json(json_encode($json), 200);
                }
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

    /**
     * @param Request $request
     * @param Conseiller $advisorFictif
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse | Response
     *
     * @Put("/network/promote-member/conseiller/{id}", name="_conseiller")
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
            if($request->isXmlHttpRequest()){
                $json = array();
                $json['item'] = array();
                return $this->json(json_encode($json), 200);
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
     *
     * @Post("/new/network")
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

    private function getEM()
    {
        return $this->getDoctrine()->getManager();
    }

}
