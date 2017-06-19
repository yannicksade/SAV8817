<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Service_apres_vente;
use APM\AchatBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Transaction_produit;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Service_apres_vente controller.
 *
 */
class Service_apres_venteController extends Controller
{
    /**
     * Liste tous les SAV enregistrés entant que client et les SAV receptionné en tant que boutique ou les SAV d'une offre
     * @param Request $request
     * @param Service_apres_vente|null $service_apres_vente
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function indexAction(Request $request, Service_apres_vente $service_apres_vente = null)
    {
        //------------------------------
        if (null == $service_apres_vente) {//create
            $this->createSecurity();
            /** @var Service_apres_vente $service_apres_vente */
            $service_apres_vente = TradeFactory::getTradeProvider("service_apres_vente");
        } else { //edit
            $this->editAndDeleteSecurity($service_apres_vente);
        }

        //------------------ Form---------------
        /** @var Session $session */
        $session = $this->get('session');
        $form = $this->createForm('APM\AchatBundle\Form\Service_apres_venteType', $service_apres_vente);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            $service_apres_vente->setClient($this->getUser());
            $em = $this->getDoctrine()->getManager();
            try {
                $em->persist($service_apres_vente);
                $em->flush();
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "Echec de l'enregistrement de la requête: " . "<strong>" . $service_apres_vente . "</strong><br>La création ou la modification a échoué!");
                $service_apres_vente = null;
                return $this->redirectToRoute('apm_achat_service_apres_vente_index');
            }
            $session->getFlashBag()->add('success', "Enregistrement de la requête: " . "<strong>" . $service_apres_vente . "</strong><br>Opération effectuée avec succès!");
            $service_apres_vente = null;
            return $this->redirectToRoute('apm_achat_service_apres_vente_index');
        }

        //-------------- Utilisateur -----------------
        $service_apres_ventes = null;

        $this->listAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $service_apres_ventes_user = null;
        $services = $user->getServicesApresVentes();
        /** @var Service_apres_vente $sav */
        foreach ($services as $sav) {
            $service_apres_ventes_user [] = array(
                'offre' => $sav->getOffre(),
                'services' => [$sav],
            );
        }
        //----------------------------------Boutiques -----------------------------------
        $boutiquesProprietaire = $user->getBoutiquesProprietaire();
        foreach ($boutiquesProprietaire as $boutique) {
            $offres = $boutique->getOffres();
            /** @var Offre $offre */
            foreach ($offres as $offre) {
                $service_apres_ventes [] = array(
                    'offre' => $offre,
                    'type' => 1,
                    'services' => $offre->getServiceApresVentes(),
                );
            }
        }
        $boutiquesGerant = $user->getBoutiquesGerant();
        foreach ($boutiquesGerant as $boutique) {
            $offres = $boutique->getOffres();
            /** @var Offre $offre */
            foreach ($offres as $offre) {
                $service_apres_ventes [] = array(
                    'offre' => $offre,
                    'type' => 0,
                    'services' => $offre->getServiceApresVentes(),
                );
            }
        }

        return $this->render('APMAchatBundle:service_apres_vente:index.html.twig', array(
            'service_apres_ventes' => $service_apres_ventes,
            'service_apres_vente' => $service_apres_vente,
            'form' => $form->createView(),
            //'boutique' => $boutique,
            //'offre' => $service_apres_vente->getOffre(),
            'service_apres_ventes_user' => $service_apres_ventes_user,
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    /**
     * @param Offre|null $offre
     */
    private function createSecurity($offre = null)
    {
        //-----------------security: L'utilisateur doit etre le client qui a acheté l'offre -----------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $clientDeOffre = false;

        if ($offre) {
            $produit_transactions = $offre->getProduitTransactions();
            /** @var Transaction_produit $produit_transaction */
            foreach ($produit_transactions as $produit_transaction) {
                $produit = $produit_transaction->getProduit();
                $client = $produit_transaction->getTransaction()->getBeneficiaire();
                if ($produit === $offre && $client === $this->getUser()) {
                    $clientDeOffre = true;
                    break;
                }
            }
            if (!$clientDeOffre) throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Service_apres_vente $service_apres_vente
     */
    private function editAndDeleteSecurity($service_apres_vente)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $client = $service_apres_vente->getClient();
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($client !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Boutique |null $boutique
     * @param Offre |null $offre
     */
    private function listAndShowSecurity($boutique = null, $offre = null)
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) throw $this->createAccessDeniedException();
        }
        if ($offre) {
            $vendeur = $offre->getVendeur();
            $boutique = $offre->getBoutique();
            $gerant = null;
            $proprietaire = null;
            if ($boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
            }
            if ($user !== $vendeur && $user !== $gerant && $user !== $proprietaire) throw $this->createAccessDeniedException();
        }

        //-----------------------------------------------------------------------------------------
    }

    public function deleteAction(Service_apres_vente $service_apres_vente)
    {
        $this->editAndDeleteSecurity($service_apres_vente);

        $em = $this->getDoctrine()->getManager();
        $em->remove($service_apres_vente);
        $em->flush();
        return $this->redirectToRoute('apm_achat_service_apres_vente_index');
    }
}
