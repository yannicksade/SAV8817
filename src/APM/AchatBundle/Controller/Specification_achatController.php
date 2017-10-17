<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Specification_achat;
use APM\AchatBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Offre;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Specification_achat controller.
 *
 */
class Specification_achatController extends Controller
{
    private $demandeRabais_filter;
    private $livraison_filter;
    private $code_filter;
    private $dateLivraisonFrom_filter;
    private $dateLivraisonTo_filter;
    private $avis_filter;
    private $echantillon_filter;
    private $offre_filter;
    private $utilisateur_filter;
    private $dateFrom_filter;
    private $dateTo_filter;

    /**
     * Liste les Specification faites par le client sur les offres
     * Liste les spécifications sur une offre
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\Response| JsonResponse
     */
    public function indexAction(Offre $offre = null)
    {
        $this->listAndShowSecurity($offre);
        if (null !== $offre) {
            $specification_achats = $offre->getSpecifications();
        } else {
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $specification_achats = $user->getSpecifications();
        }
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $this->demandeRabais_filter = $request->request->has('demandeRabais_filter') ? $request->request->get('demandeRabais_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->livraison_filter = $request->request->has('livraison_filter') ? $request->request->get('livraison_filter') : "";
            $this->dateLivraisonFrom_filter = $request->request->has('dateLivraisonFrom_filter') ? $request->request->get('dateLivraisonFrom_filter') : "";
            $this->dateLivraisonTo_filter = $request->request->has('dateLivraisonTo_filter') ? $request->request->get('dateLivraisonTo_filter') : "";
            $this->dateFrom_filter = $request->request->has('dateFrom_filter') ? $request->request->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->request->has('dateTo_filter') ? $request->request->get('dateTo_filter') : "";
            $this->avis_filter = $request->request->has('avis_filter') ? $request->request->get('avis_filter') : "";
            $this->echantillon_filter = $request->request->has('echantillon_filter') ? $request->request->get('echantillon_filter') : "";
            $this->offre_filter = $request->request->has('offre_filter') ? $request->request->get('offre_filter') : "";
            $this->utilisateur_filter = $request->request->has('utilisateur_filter') ? $request->request->get('utilisateur_filter') : "";

            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $json = array();
            $json['items'] = array();
            $iTotalRecords = count($specification_achats);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $specification_achats = $this->handleResults($specification_achats, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            /** @var Specification_achat $specification */
            foreach ($specification_achats as $specification) {
                array_push($json['items'], array(
                    'id' => $specification->getId(),
                    'code' => $specification->getCode(),
                    'avis' => substr($specification->getAvis(), 100) . "...",
                ));
            }
            return $this->json(json_encode($json), 200);
        }
        return $this->render('APMAchatBundle:specification_achat:index.html.twig', array(
            'specification_achats' => $specification_achats,
            'offre' => $offre,
        ));
    }

    /**
     * @param Offre |null $offre
     */
    private function listAndShowSecurity($offre = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($offre) {
            $user = $this->getUser();
            $vendeur = $offre->getVendeur();
            $boutique = $offre->getBoutique();
            $gerant = null;
            $proprietaire = null;
            if ($boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
            }
            if ($user !== $vendeur && $user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }

        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $specifications
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($specifications, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($specifications === null) return array();

        if ($this->code_filter != null) {
            $specifications = $specifications->filter(function ($e) {//filtrage select
                /** @var Specification_achat $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->livraison_filter != null) {
            $specifications = $specifications->filter(function ($e) {//filtrage select
                /** @var Specification_achat $e */
                return $e->getLivraison() === boolval($this->livraison_filter);
            });
        }
        if ($this->demandeRabais_filter != null) {
            $specifications = $specifications->filter(function ($e) {//filtrage select
                /** @var Specification_achat $e */
                return $e->getDemandeRabais() === boolval($this->demandeRabais_filter);
            });
        }
        if ($this->echantillon_filter != null) {
            $specifications = $specifications->filter(function ($e) {//filtrage select
                /** @var Specification_achat $e */
                return $e->getEchantillon() === boolval($this->echantillon_filter);
            });
        }
        if ($this->utilisateur_filter != null) {
            $specifications = $specifications->filter(function ($e) {//filter with the begining of the entering word
                /** @var Specification_achat $e */
                $str1 = $e->getUtilisateur()->getCode();
                $str2 = $this->utilisateur_filter;
                return strcasecmp($str1, $str2) === 0 ? true : false;
            });
        }
        if ($this->offre_filter != null) {
            $specifications = $specifications->filter(function ($e) {//filter with the begining of the entering word
                /** @var Specification_achat $e */
                $str1 = $e->getOffre()->getCode();
                $str2 = $this->offre_filter;
                return strcasecmp($str1, $str2) === 0 ? true : false;
            });
        }
        if ($this->avis_filter != null) {
            $specifications = $specifications->filter(function ($e) {//search for occurences in the text
                /** @var Specification_achat $e */
                $subject = $e->getAvis();
                $pattern = $this->avis_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->dateLivraisonFrom_filter != null) {
            $specifications = $specifications->filter(function ($e) {//start date
                /** @var Specification_achat $e */
                $dt1 = (new \DateTime($e->getDateLivraisonSouhaite()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateLivraisonFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateLivraisonTo_filter != null) {
            $specifications = $specifications->filter(function ($e) {//end date
                /** @var Specification_achat $e */
                $dt = (new \DateTime($e->getDateCreation()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateLivraisonTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->dateFrom_filter != null) {
            $specifications = $specifications->filter(function ($e) {//start date
                /** @var Specification_achat $e */
                $dt1 = (new \DateTime($e->getDateCreation()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $specifications = $specifications->filter(function ($e) {//end date
                /** @var Specification_achat $e */
                $dt = (new \DateTime($e->getDateCreation()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }

        $specifications = ($specifications !== null) ? $specifications->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $specifications, function ($e1, $e2) {
            /**
             * @var Specification_achat $e1
             * @var Specification_achat $e2
             */
            $dt1 = $e1->getDateCreation()->getTimestamp();
            $dt2 = $e2->getDateCreation()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $specifications = array_slice($specifications, $iDisplayStart, $iDisplayLength, true);

        return $specifications;
    }

    /**
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response| JsonResponse
     */
    public function newAction(Request $request, Offre $offre)
    {
        $this->createSecurity();
        /** @var Specification_achat $specification_achat */
        $specification_achat = TradeFactory::getTradeProvider("specification_achat");
        $form = $this->createForm('APM\AchatBundle\Form\Specification_achatType', $specification_achat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $specification_achat->setUtilisateur($this->getUser());
            $specification_achat->setOffre($offre);
            $em = $this->getDoctrine()->getManager();
            $em->persist($specification_achat);
            $em->flush();
            if($request->isXmlHttpRequest()){
                $json = array();
                $json['item'] = array();
                return $this->json(json_encode($json),200);
            }
            return $this->redirectToRoute('apm_achat_specification_achat_show', array('id' => $specification_achat->getId()));
        }

        return $this->render('APMAchatBundle:specification_achat:new.html.twig', array(
            'specification_achat' => $specification_achat,
            'form' => $form->createView(),
        ));
    }

    private function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Voir une specification faite
     *
     * Finds and displays a Specification_achat entity.
     * @param Request $request
     * @param Specification_achat $specification
     * @return \Symfony\Component\HttpFoundation\Response| JsonResponse
     */
    public function showAction(Request $request, Specification_achat $specification)
    {
        $this->listAndShowSecurity();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $specification->getId(),
                'code' => $specification->getCode(),
                'demandeRabais' => $specification->getDemandeRabais(),
                'livrable' => $specification->getLivraison(),
                'dateCreation' => $specification->getDateCreation()->format('d-m-Y H:i'),
                'dateLivraisonSouhaite' => $specification->getDateLivraisonSouhaite()->format('d-m-Y H:i'),
                'avis' => $specification->getAvis(),
                'echantillon' => $specification->getEchantillon(),
                'offre' => $specification->getOffre()->getId(),
                'utilisateur' => $specification->getUtilisateur()->getId(),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($specification);
        return $this->render('APMAchatBundle:specification_achat:show.html.twig', array(
            'specification_achat' => $specification,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Specification_achat entity.
     *
     * @param Specification_achat $specification_achat The Specification_achat entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Specification_achat $specification_achat)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_achat_specification_achat_delete', array('id' => $specification_achat->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Specification_achat entity.
     * @param Request $request
     * @param Specification_achat $specification_achat
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Specification_achat $specification_achat)
    {
        $this->editAndDeleteSecurity($specification_achat);
        $deleteForm = $this->createDeleteForm($specification_achat);
        $editForm = $this->createForm('APM\AchatBundle\Form\Specification_achatType', $specification_achat);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()
            || $request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            if ($request->isXmlHttpRequest()) {
                $json = array();
                $json['item'] = array();
                $property = $request->request->get('name');
                $value = $request->request->get('value');
                switch ($property) {
                    case 'demandeRabais':
                        $specification_achat->setDemandeRabais($value);
                        break;
                    case 'livraison':
                        $specification_achat->setLivraison($value);
                        break;
                    case 'avis':
                        $specification_achat->setAvis($value);
                        break;
                    case 'dateLivraison' :
                        $specification_achat->setDateLivraisonSouhaite($value);
                        break;
                    case 'echantillon' :
                        $specification_achat->setEchantillon($value);
                        break;
                    default:
                        $session->getFlashBag()->add('info', "<strong> Aucune mis à jour effectuée</strong>");
                        return $this->json(json_encode(["item" => null]), 205);
                }
                $em->flush();
                $session->getFlashBag()->add('success', "Mise à jour propriété : <strong>" . $property . "</strong> réf. Spécification achat :" . $specification_achat->getCode() . "<br> Opération effectuée avec succès!");
                return $this->json(json_encode($json), 200);
            }
            $em->flush();
            $session->getFlashBag()->add('success', "Mise à jour propriété : <strong>" . $property . "</strong> réf. Spécification achat :" . $specification_achat->getCode() . "<br> Opération effectuée avec succès!");
            return $this->redirectToRoute('apm_achat_specification_achat_show', array('id' => $specification_achat->getId()));
        }
        return $this->render('APMAchatBundle:specification_achat:edit.html.twig', array(
            'specification_achat' => $specification_achat,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Specification_achat $specification_achat
     */
    private function editAndDeleteSecurity($specification_achat)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in  # granted even through remembering cookies
        */
        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') || $user !== $specification_achat->getUtilisateur()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Deletes a Specification_achat entity.
     * @param Request $request
     * @param Specification_achat $specification_achat
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     */
    public function deleteAction(Request $request, Specification_achat $specification_achat)
    {
        $this->editAndDeleteSecurity($specification_achat);
        /** @var Session $session */
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        try {
            if ($request->isXmlHttpRequest()) {
                $em->remove($specification_achat);
                $em->flush();
                $json = array();
                $json['item'];
                $session->getFlashBag()->add('success', "Suppression de la spécification : " . "<strong>" . $specification_achat->getCode() . "</strong><br>Opération effectuée avec succès!");
                return $this->json(json_encode($json), 200);
            }
            $form = $this->createDeleteForm($specification_achat);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->remove($specification_achat);
                $em->flush();
            }
            $session->getFlashBag()->add('success', "Suppression de la spécification: " . "<strong>" . $specification_achat->getCode() . "</strong><br>Opération effectuée avec succès!");
            return $this->redirectToRoute('apm_achat_specification_achat_index');
        } catch (ConstraintViolationException $cve) {
            $session->getFlashBag()->add('danger', "Echec de la suppression de: " . "<strong>" . $specification_achat->getCode() . "</strong><br>L'opération de suppression du groupe d'offre a échoué!");
            return $this->redirectToRoute('apm_achat_groupe_index');
        } catch (AccessDeniedException $ads) {
            $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
            return $this->json(json_encode(["item" => null]));
        }

    }

    /**
     * @param Specification_achat $specification_achat
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteFromListAction(Specification_achat $specification_achat)
    {
        $this->editAndDeleteSecurity($specification_achat);
        $em = $this->getDoctrine()->getManager();
        $em->remove($specification_achat);
        $em->flush();

        return $this->redirectToRoute('apm_achat_specification_achat_index');
    }
}
