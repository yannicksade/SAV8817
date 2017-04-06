<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 28/02/2017
 * Time: 01:17
 */

namespace APM\CoreBundle\Controller;

use APM\UserBundle\Entity\Utilisateur_avm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserDashboardController extends Controller
{
//traite ici la disposition de l'utilisateur dans son espace d'administration.
    public function showAction()
    {

        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!'); //proteger l'acces à un controller

        $userData = array();
        $systemData = array();
        //$loginData = $this->login($request);
        return $this->render(':client/dashboard:user-dashboard.html.twig', array(//  'login' => $loginData,
                'data' => $userData, 'system' => $systemData,));

    }

    public function showGroupeOffreAction()
    {


        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) { # granted even through remembering cookies
            throw $this->createAccessDeniedException();
        }
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $em = $this->getEM();
        $groupesOffresUser = $em->getRepository('APMAchatBundle:Groupe_offre')->findBy(['createur' => $user->getId()], ['dateDeVigueur' => 'DESC']); // 'ASC or DESC; limit (ex: limit: 10 de 0-9) or null; offset (ex: offset: 0 or 10) or null e

        return $this->render('APMCoreBundle::index.html.twig', ['groupe_offres' => $groupesOffresUser]);
    }

    private function getEM()
    {

        return $this->getDoctrine()->getManager();
    }
}