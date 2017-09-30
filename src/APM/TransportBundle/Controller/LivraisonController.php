<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Livraison;
use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Transaction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Livraison controller.
 *
 */
class LivraisonController extends Controller
{
    private $code_filter;
    private $livreur_boutique;
    private $description_filter;
    private $etat_filter;
    private $priorite_filter;
    private $valide_filter;
    private $dateEnregistrementTo_filter;
    private $dateEnregistrementFrom_filter;
    private $datePrevueFrom_filter;
    private $datePrevueTo_filter;

    /**
     * Liste les livraisons enregistrées par un utilisateur ou par une boutique
     * les livraisons se font uniquement sur les opérations effectuées...
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response| JsonResponse
     */
    public function indexAction(Request $request, Boutique $boutique = null)
    {
        $this->listeAndShowSecurity($boutique);
        if (null !== $boutique) {
            $livraisons = $boutique->getLivraisons();
        } else {
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $livraisons = $user->getLivraisons();
        }
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['items'] = array();
            $this->datePrevueFrom_filter = $request->request->has('datePrevueFrom_filter') ? $request->request->get('datePrevueFrom_filter') : "";
            $this->datePrevueTo_filter = $request->request->has('datePrevueTo_filter') ? $request->request->get('datePrevueTo_filter') : "";
            $this->dateEnregistrementFrom_filter = $request->request->has('dateEnregistrementFrom_filter') ? $request->request->get('dateEnregistrementFrom_filter') : "";
            $this->dateEnregistrementTo_filter = $request->request->has('dateEnregistrementTo_filter') ? $request->request->get('dateEnregistrementTo_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->livreur_boutique = $request->request->has('livreur_boutique') ? $request->request->get('livreur_boutique') : "";
            $this->description_filter = $request->request->has('description_filter') ? $request->request->get('description_filter') : "";
            $this->etat_filter = $request->request->has('etat_filter') ? $request->request->get('etat_filter') : "";
            $this->priorite_filter = $request->request->has('priorite_filter') ? $request->request->get('priorite_filter') : "";
            $this->valide_filter = $request->request->has('valide_filter') ? $request->request->get('valide_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $iTotalRecords = count($livraisons);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $livraisons = $this->handleResults($livraisons, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            /** @var Livraison $livraison */
            foreach ($livraisons as $livraison) {
                array_push($json['items'], array(
                    'value' => $livraison->getId(),
                    'text' => $livraison->getCode(),
                ));
            }
            return $this->json(json_encode($json), 200);
        }
        return $this->render('APMTransportBundle:livraison:index.html.twig', array(
            'livraisons' => $livraisons,
            'boutique' => $boutique,
        ));
    }

    /**
     * @param Collection $livraisons
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($livraisons, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($livraisons === null) return array();

        if ($this->code_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//filtrage select
                /** @var Livraison $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->etat_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//filtrage select
                /** @var Livraison $e */
                return $e->getEtatLivraison() === $this->etat_filter;
            });
        }

        if ($this->valide_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//search for occurences in the text
                /** @var Livraison $e */
                return $e->getValide() === boolval($this->valide_filter);
            });
        }

        if ($this->datePrevueFrom_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//start date
                /** @var Livraison $e */
                $dt1 = (new \DateTime($e->getDateEtHeureLivraison()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->datePrevueFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->datePrevueTo_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//end date
                /** @var Livraison $e */
                $dt = (new \DateTime($e->getDateEtHeureLivraison()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->datePrevueTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->dateEnregistrementFrom_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//start date
                /** @var Livraison $e */
                $dt1 = (new \DateTime($e->getDateEnregistrement()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateEnregistrementFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateEnregistrementTo_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//end date
                /** @var Livraison $e */
                $dt = (new \DateTime($e->getDateEnregistrement()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateEnregistrementTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->description_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//search for occurences in the text
                /** @var Livraison $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        $livraisons = ($livraisons !== null) ? $livraisons->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $livraisons, function ($e1, $e2) {
            /**
             * @var Livraison $e1
             * @var Livraison $e2
             */
            $dt1 = $e1->getDateEtHeureLivraison()->getTimestamp();
            $dt2 = $e2->getDateEtHeureLivraison()->getTimestamp();
            if ($dt1 === $dt2) $r = $e1->getPriorite() <= $e2->getPriorite() ? 1 : -1;
            else $r = $dt1 <= $dt2 ? 1 : -1;
            return $r;
        });

        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $livraisons = array_slice($livraisons, $iDisplayStart, $iDisplayLength, true);

        return $livraisons;
    }


    /**
     * @param Boutique $boutique
     * @param Utilisateur_avm |null $beneficiaire
     */
    private function listeAndShowSecurity($boutique, $beneficiaire = null)
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        } else {//donne la possibilité au bénéficiaire de voir les détails de la livraison
            if ($beneficiaire) {
                if ($user !== $beneficiaire) {
                    throw $this->createAccessDeniedException();
                }
            }
        }

        //------------------------------------------------------------------------------------------
    }


    /**
     * @ParamConverter("transaction", options={"mapping":{"transaction_id":"id"}})
     * Creates a new Livraison entity.
     * @param Request $request
     * @param Boutique $boutique
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response| JsonResponse
     */
    public function newAction(Request $request, Boutique $boutique = null, Transaction $transaction = null)
    {
        $tr=[];
        if(null !== $transaction) $tr = new ArrayCollection([$transaction]);
        $this->createSecurity($boutique, $tr);
        /** @var Livraison $livraison */
        $livraison = TradeFactory::getTradeProvider("livraison");
        $form = $this->createForm('APM\TransportBundle\Form\LivraisonType', $livraison);
        if(null !== $transaction)$form->remove('operations');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $livraison->setUtilisateur($this->getUser());
            $livraison->setBoutique($boutique);
            if(null !== $transaction){
                $transactions = $tr;
            }else{
                $transactions = $livraison->getOperations();
            }
            $this->createSecurity($boutique, $transactions);
            /** @var Transaction $transaction */
            foreach ($transactions as $transaction) {
                $transaction->setShipped(true);
                $transaction->setLivraison($livraison);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($livraison);
            $em->flush();
            if($request->isXmlHttpRequest()){
                $json = array();
                $json['item'] =array();
                return $this->json(json_encode($json), 200);
            }
            
            return $this->redirectToRoute('apm_transport_livraison_show', array('id' => $livraison->getId()));
        }

        return $this->render('APMTransportBundle:livraison:new.html.twig', array(
            'livraison' => $livraison,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @param Boutique $boutique
     * Verifie si la boutique appartient à son proprietaire ou le gerant
     * @param Collection $transactions
     */
    private function createSecurity($boutique, $transactions = null)
    {
        //--------security: verifie si l'utilisateur courant est le gerant de la boutique qui cree la livraison---------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        if (null !== $boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        if (null !== $transactions) {
            /** @var Transaction $operation */
            foreach ($transactions as $operation) {//ne pas livrer une opération plus d'une fois et
                if ($operation->isShipped() || $boutique !== $operation->getBoutique() || $user !== $operation->getAuteur() || null === $operation->getTransactionProduits()) {
                    throw $this->createAccessDeniedException();
                }
            }
        }
        //--------------------------------------------------------------------------------------------------------------
    }

    /**
     * @ParamConverter("transaction", options={"mapping":{"transaction_id":"id"}})
     * voir un livraison
     * @param Request $request
     * @param Livraison $livraison
     * @param Transaction $transaction
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Livraison $livraison, Transaction $transaction = null)
    {
        if ($transaction) {
            $this->listeAndShowSecurity(null, $transaction->getBeneficiaire());
        } else {
            $this->listeAndShowSecurity($livraison->getBoutique());
        }
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $livraison->getId(),
                'date' => $livraison->getDateEtHeureLivraison()->format('d-m-Y H:i'),
                'code' => $livraison->getCode(),
                'description' => $livraison->getDescription(),
                'dateEnregistrement' => $livraison->getDateEnregistrement()->format('d-m-Y H:i'),
                'etat' => $livraison->getEtatLivraison(),
                'priorite' => $livraison->getPriorite(),
                'valide' => $livraison->isValide(),
                'livreur' => $livraison->getLivreur()->getMatricule()
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($livraison);
        return $this->render('APMTransportBundle:livraison:show.html.twig', array(
            'livraison' => $livraison,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Livraison entity.
     *
     * @param Livraison $livraison The Livraison entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Livraison $livraison)
    {
        $this->editAndDeleteSecurity($livraison);
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_transport_livraison_delete', array('id' => $livraison->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Livraison $livraison
     */
    private function editAndDeleteSecurity($livraison)
    {//----- security : au cas ou il s'agirait d'une boutique vérifier le droit de l'utilisateur --------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $boutique = $livraison->getBoutique();
        if (null !== $boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire && $user !== $livraison->getUtilisateur()) {
                throw $this->createAccessDeniedException();
            }
        }
        //--------------------------------------------------------------------------------------------------------------

    }

    /**
     * Displays a form to edit an existing Livraison entity.
     * @param Request $request
     * @param Livraison $livraison
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response| JsonResponse
     */
    public function editAction(Request $request, Livraison $livraison)
    {
        $this->editAndDeleteSecurity($livraison);
        $editForm = $this->createForm('APM\TransportBundle\Form\LivraisonType', $livraison);
        $editForm->handleRequest($request);
        /** @var Session $session */
        $session = $request->getSession();
        if ($editForm->isSubmitted() && $editForm->isValid() 
            || $request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $transactions = $livraison->getOperations();
            /** @var Transaction $transaction */
            foreach ($transactions as $transaction) {
                $transaction->setShipped(true);
                $transaction->setLivraison($livraison);
            }
            try {
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json['item'] = array();
                    $property = $request->request->get('name');
                    $value = $request->request->get('value');
                    switch ($property) {
                        case 'dateLivraison':
                            $livraison->setDateEtHeureLivraison($value);
                            break;
                        case 'description':
                            $livraison->setDescription($value);
                            break;
                        case 'etat':
                            $livraison->setEtatLivraison($value);
                            break;
                        case 'priorite':
                            $livraison->setPriorite($value);
                            break;
                        case 'valide':
                            $livraison->setValide($value);
                            break;
                        case 'transporteur':
                            /** @var Profile_transporteur $transporteur */
                            $transporteur = $em->getRepository('APMTransportBundle:profile_transporteur')->find($value);
                            $livraison->setLivreur($transporteur);
                            break;
                        case 'transaction':
                            /** @var Transaction $transaction */
                            $transaction= $em->getRepository('APMVenteBundle:Transaction')->find($value);
                            $transaction->setLivraison($livraison);
                            $livraison->addOperation($transaction);
                            break;
                        default:
                            $session->getFlashBag()->add('info', "<strong> Aucune mise à jour effectuée</strong>");
                            return $this->json(json_encode(["item" => null]), 205);
                    }
                    $em->flush();
                    $session->getFlashBag()->add('success', "Modification de la livraison : <strong>" . $property . "</strong> réf. livraison :" . $livraison->getCode() . "<br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                $em->flush();
                return $this->redirectToRoute('apm_transport_livraison_show', array('id' => $livraison->getId()));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        $deleteForm = $this->createDeleteForm($livraison);
        return $this->render('APMTransportBundle:livraison:edit.html.twig', array(
            'livraison' => $livraison,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Livraison entity.
     * @param Request $request
     * @param Livraison $livraison
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     */
    public function deleteAction(Request $request, Livraison $livraison)
    {
        $this->editAndDeleteSecurity($livraison);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $em->remove($livraison);
            $em->flush();
            $json = array();
            $json['item'] = array();
            return $this->json(json_encode($json), 200);
        }
        $form = $this->createDeleteForm($livraison);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($livraison);
            $em->flush();
        }

        return $this->redirectToRoute('apm_transport_livraison_index');
    }

    public function deleteFromListAction(Livraison $livraison)
    {
        $this->editAndDeleteSecurity($livraison);
        $em = $this->getDoctrine()->getManager();
        $em->remove($livraison);
        $em->flush();

        return $this->redirectToRoute('apm_transport_livraison_index');
    }
}
