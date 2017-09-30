<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Commissionnement;
use APM\MarketingDistribueBundle\Entity\Conseiller_boutique;
use APM\MarketingDistribueBundle\Entity\Quota;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Admin;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Commissionnement controller.
 *
 */
class CommissionnementController extends Controller
{
    private $code_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $libelle_filter;
    private $description_filter;
    private $conseiller_filter;
    private $commission_filter;
    private $creditDepenseFrom_filter;
    private $creditDepenseTo_filter;
    private $quantiteTo_filter;
    private $quantiteFrom_filter;

    /**
     * Liste les commissionnements d'un conseiller ou d'une boutique pour jouir il faut avoir definir son profile conseiller
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function indexAction(Request $request, Boutique $boutique = null)
    {

        if (null !== $boutique) {
            $this->listAndShowSecurity(null, $boutique);
            $boutiques_commissionnements = array();
            $boutiqueConseillers = $boutique->getBoutiqueConseillers();
            if (null !== $boutiqueConseillers) {
                /** @var Conseiller_boutique $boutiqueConseiller */
                foreach ($boutiqueConseillers as $boutiqueConseiller) {
                    $boutiques_commissionnements [] = $boutiqueConseiller->getCommissionnements();
                }
            }
        } else {
            $this->listAndShowSecurity();
            $boutiques_commissionnements = array();
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $conseiller = $user->getProfileConseiller();
            if (null !== $conseiller) {
                $conseiller_boutiques = $conseiller->getConseillerBoutiques();
                if (null !== $conseiller_boutiques) {
                    /** @var Conseiller_boutique $conseiller_boutique */
                    foreach ($conseiller_boutiques as $conseiller_boutique) {
                        $boutiques_commissionnements [] = $conseiller_boutique->getCommissionnements();
                    }
                }
            }
        }
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['items'] = array();
            $this->creditDepenseFrom_filter = $request->request->has('creditDepenseFrom_filter') ? $request->request->get('creditDepenseFrom_filter') : "";
            $this->creditDepenseTo_filter = $request->request->has('creditDepenseTo_filter') ? $request->request->get('creditDepenseTo_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->dateFrom_filter = $request->request->has('dateFrom_filter') ? $request->request->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->request->has('dateTo_filter') ? $request->request->get('dateTo_filter') : "";
            $this->libelle_filter = $request->request->has('libelle_filter') ? $request->request->get('libelle_filter') : "";
            $this->description_filter = $request->request->has('description_filter') ? $request->request->get('description_filter') : "";
            $this->quantiteFrom_filter = $request->request->has('quantiteFrom_filter') ? $request->request->get('quantiteFrom_filter') : "";
            $this->quantiteTo_filter = $request->request->has('quantiteTo_filter') ? $request->request->get('quantiteTo_filter') : "";
            $this->conseiller_filter = $request->request->has('conseiller_filter') ? $request->request->get('conseiller_filter') : "";
            $this->commission_filter = $request->request->has('commission_filter') ? $request->request->get('commission_filter') : "";

            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;

            $boutiques_commissionnements = new ArrayCollection($boutiques_commissionnements);
            $iTotalRecords = count($boutiques_commissionnements);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $boutiques_commissionnements = $this->handleResults($boutiques_commissionnements, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            /** @var Commissionnement $commissionnement */
            foreach ($boutiques_commissionnements as $commissionnement) {
                array_push($json['items'], array(
                    'value' => $commissionnement->getId(),
                    'text' => $commissionnement->getCode(),
                ));
            }
            return $this->json(json_encode($json), 200);
        }
        return $this->render('APMMarketingDistribueBundle:commissionnement:index.html.twig', array(
            'boutiques_commissionnements' => $boutiques_commissionnements,
        ));
    }

    /**
     * @param Collection $commissionnements
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($commissionnements, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($commissionnements === null) return array();

        if ($this->conseiller_filter != null) {
            $commissionnements = $commissionnements->filter(function ($e) {//filtrage select
                /** @var Commissionnement $e */
                return $e->getConseillerBoutique()->getConseiller()->getMatricule() === $this->conseiller_filter;
            });
        }
        if ($this->commission_filter != null) {
            $commissionnements = $commissionnements->filter(function ($e) {//filtrage select
                /** @var Commissionnement $e */
                return $e->getCommission()->getCode() === $this->commission_filter;
            });
        }
        if ($this->creditDepenseFrom_filter != null) {
            $commissionnements = $commissionnements->filter(function ($e) {//filtrage select
                /** @var Commissionnement $e */
                return $e->getCreditDepense() <= $this->creditDepenseFrom_filter;
            });
        }
        if ($this->creditDepenseTo_filter != null) {
            $commissionnements = $commissionnements->filter(function ($e) {//filtrage select
                /** @var Commissionnement $e */
                return $e->getCreditDepense() >= $this->creditDepenseTo_filter;
            });
        }
        if ($this->quantiteTo_filter != null) {
            $commissionnements = $commissionnements->filter(function ($e) {//filtrage select
                /** @var Commissionnement $e */
                return $e->getQuantite() >= $this->quantiteTo_filter;
            });
        }
        if ($this->quantiteFrom_filter != null) {
            $commissionnements = $commissionnements->filter(function ($e) {//filtrage select
                /** @var Commissionnement $e */
                return $e->getQuantite() <= $this->quantiteFrom_filter;
            });
        }
        if ($this->dateFrom_filter != null) {
            $commissionnements = $commissionnements->filter(function ($e) {//start date
                /** @var Commissionnement $e */
                $dt1 = (new \DateTime($e->getDateCreation()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $commissionnements = $commissionnements->filter(function ($e) {//end date
                /** @var Commissionnement $e */
                $dt = (new \DateTime($e->getDateCreation()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->libelle_filter != null) {
            $commissionnements = $commissionnements->filter(function ($e) {//search for occurences in the text
                /** @var Commissionnement $e */
                $subject = $e->getLibelle();
                $pattern = $this->libelle_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->description_filter != null) {
            $commissionnements = $commissionnements->filter(function ($e) {//search for occurences in the text
                /** @var Commissionnement $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $commissionnements = ($commissionnements !== null) ? $commissionnements->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $commissionnements, function ($e1, $e2) {
            /**
             * @var Commissionnement $e1
             * @var Commissionnement $e2
             */
            $dt1 = $e1->getDateCreation()->getTimestamp();
            $dt2 = $e2->getDateCreation()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });

        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $commissionnements = array_slice($commissionnements, $iDisplayStart, $iDisplayLength, true);

        return $commissionnements;
    }

    /**
     * @param Commissionnement |null $commissionnement
     * @param Boutique $boutique
     */
    private function listAndShowSecurity($commissionnement = null, $boutique = null)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted(['ROLE_CONSEILLER', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $gerant = null;
        $proprietaire = null;
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $conseiller = $user->getProfileConseiller();
        if ($conseiller) $conseiller = $conseiller->getUtilisateur();
        if ($commissionnement) {
            $boutique = $commissionnement->getCommission()->getBoutiqueProprietaire();
            $conseiller = $commissionnement->getConseillerBoutique()->getConseiller()->getUtilisateur();
            $gerant = null;
            $proprietaire = null;
        }
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            $conseiller = null;
        }
        if ($user !== $conseiller && $user !== $gerant && $user !== $proprietaire) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * Une boutique créé des commissionnements pour des conseiller de la boutique
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function newAction(Request $request, Boutique $boutique)
    {
        $this->createSecurity($boutique);
        /** @var Commissionnement $commissionnement */
        $commissionnement = TradeFactory::getTradeProvider("commissionnement");
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\CommissionnementType', $commissionnement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($boutique, $commissionnement->getCommission());
            $em = $this->getDoctrine()->getManager();
            $em->persist($commissionnement);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                $json = array();
                $json['item'] = array();
                return $this->json(json_encode($json), 200);
            }
            return $this->redirectToRoute('apm_marketing_commissionnement_show', array('id' => $commissionnement->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:commissionnement:new.html.twig', array(
            'commissionnement' => $commissionnement,
            'form' => $form->createView(),
            'boutique' => $boutique,
        ));
    }

    /**
     * @param Boutique $boutique
     * @param Quota $quota
     */
    private function createSecurity($boutique, $quota = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have the required role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /* ensure that the user is logged in
        *  and that the one is the owner
        */
        //la boutique pour laquelle le conseiller beneficie les commissionnements doit être la même qui offre le Quota
        $user = $this->getUser();
        $proprietaire = $boutique->getProprietaire();
        $gerant = $boutique->getGerant();
        if (null !== $quota && $quota->getBoutiqueProprietaire() !== $boutique || $user !== $gerant && $user !== $proprietaire)
            throw $this->createAccessDeniedException();
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Commissionnement entity.
     * @param Request $request
     * @param Commissionnement $commissionnement
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Commissionnement $commissionnement)
    {
        $this->listAndShowSecurity($commissionnement);
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $commissionnement->getId(),
                'code' => $commissionnement->getCode(),
                'credit' => $commissionnement->getCreditDepense(),
                'date' => $commissionnement->getDateCreation()->format("d/m/Y - H:i"),
                'libelle' => $commissionnement->getLibelle(),
                'description' => $commissionnement->getDescription(),
                'quantite' => $commissionnement->getQuantite(),
                'conseillerBoutique' => $commissionnement->getConseillerBoutique()->getId(),
                'commission' => $commissionnement->getCommission()->getCode(),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($commissionnement);
        return $this->render('APMMarketingDistribueBundle:commissionnement:show.html.twig', array(
            'commissionnement' => $commissionnement,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Commissionnement entity.
     *
     * @param Commissionnement $commissionnement The Commissionnement entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Commissionnement $commissionnement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_commissionnement_delete', array('id' => $commissionnement->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Commissionnement entity.
     * @param Request $request
     * @param Commissionnement $commissionnement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function editAction(Request $request, Commissionnement $commissionnement)
    {
        $this->editSecurity();
        $deleteForm = $this->createDeleteForm($commissionnement);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\CommissionnementType', $commissionnement);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() ||
            $request->isXmlHttpRequest() && $request->isMethod('POST')
        ) {
            $this->editSecurity();
            $em = $this->getDoctrine()->getManager();
            try {
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json['item'] = array();
                    $property = $request->request->get('name');
                    $value = $request->request->get('value');
                    switch ($property) {
                        case 'creditDepense':
                            $commissionnement->setCreditDepense($value);
                            break;
                        case 'libelle':
                            $commissionnement->setLibelle($value);
                            break;
                        case 'quantite':
                            $commissionnement->setQuantite($value);
                            break;
                        case 'description':
                            $commissionnement->setDescription($value);
                            break;
                        default:
                            $session->getFlashBag()->add('info', "<strong> Aucune mise à jour effectuée </strong>");
                            return $this->json(json_encode(["item" => null]), 205);
                    }
                    $em->flush();
                    $session->getFlashBag()->add('success', "Modification du conseiller boutique : <strong>" . $property . "</strong> <br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                $em->flush();
                return $this->redirectToRoute('apm_marketing_commissionnement_show', array('id' => $commissionnement->getId()));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        return $this->render('APMMarketingDistribueBundle:commissionnement:edit.html.twig', array(
            'commissionnement' => $commissionnement,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    private function editSecurity()
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Unable to access this page!');
        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
            || !($user instanceof Admin)
        ) throw $this->createAccessDeniedException();
    }

    /**
     * Supprimer à partir d'un formulaire
     * @param Request $request
     * @param Commissionnement $commissionnement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     */
    public function deleteAction(Request $request, Commissionnement $commissionnement)
    {
        $this->deleteSecurity($commissionnement);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $em->remove($conseiller_boutique);
            $em->flush();
            $json = array();
            $json['item'] = array();
            return $this->json(json_encode($json), 200);
        }

        $form = $this->createDeleteForm($commissionnement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->deleteSecurity($commissionnement);
            $em->remove($commissionnement);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_commissionnement_index');
    }
    //-------------------------------------------------------

    /**
     * Le conseiller peut supprimer ses commissionnement
     * @param Commissionnement $commissionnement
     */
    private function deleteSecurity($commissionnement)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            || ($commissionnement->getConseillerBoutique()->getConseiller()->getUtilisateur() !== $user)
        ) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    public function deleteFromListAction(Commissionnement $commissionnement)
    {
        $this->deleteSecurity($commissionnement);
        $em = $this->getDoctrine()->getManager();
        $em->remove($commissionnement);
        $em->flush();

        return $this->redirectToRoute('apm_marketing_commissionnement_index');
    }
    //----------------------------------------------------------------------------------------
}

