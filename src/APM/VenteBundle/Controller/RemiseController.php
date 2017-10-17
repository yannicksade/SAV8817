<?php

namespace APM\VenteBundle\Controller;

use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Remise;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Remise controller.
 *
 */
class RemiseController extends Controller
{
    private $dateExpiration_filter;
    private $code_filter;
    private $offre_filter;
    private $etat_filter;
    private $valeurMin_filter;
    private $valeurMax_filter;
    private $quantiteMin_filter;
    private $nombreUtilisation_filter;
    private $permanence_filter;
    private $restreint_filter;

    /** @ParamConverter("offre", options={"mapping": {"offre_id":"id"}})
     * Liste toutes les remises appliquées sur une offre ou les remises créées par un vendeurs
     * @param Request $request
     * @param Boutique|null $boutique
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, Boutique $boutique = null, Offre $offre = null)
    {
        /** @var Session $session */
        $session = $request->getSession();
        if (null !== $offre) {//liste les remises sur l'offre
            $this->listAndShowSecurity($offre);
            $offres [] = $offre;

        } elseif (null !== $boutique) {
            $this->listAndShowSecurity(null, $boutique);
            $offres = $boutique->getOffres();
        } else {//liste les remises d'un utilisateur
            $this->listAndShowSecurity();
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $offres = $user->getOffres();
        }
        $remises = new ArrayCollection();
        if (null !== $offres) {
            /** @var Offre $o */
            foreach ($offres as $o) {
                foreach ($o->getRemises() as $r) {
                    $remises->add($r);
                }
            }
        }
        if ($request->isXmlHttpRequest()) {
            $this->dateExpiration_filter = $request->request->has('dateExpiration_filter') ? $request->request->get('dateExpiration_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->offre_filter = $request->request->has('offre_filter') ? $request->request->get('offre_filter') : "";
            $this->etat_filter = $request->request->has('etat_filter') ? $request->request->get('etat_filter') : "";
            $this->valeurMin_filter = $request->request->has('valeurMin_filter') ? $request->request->get('valeurMin_filter') : "";
            $this->valeurMax_filter = $request->request->has('valeurMax_filter') ? $request->request->get('valeurMax_filter') : "";
            $this->nombreUtilisation_filter = $request->request->has('nombreUtilisation_filter') ? $request->request->get('nombreUtilisation_filter') : "";
            $this->quantiteMin_filter = $request->request->has('quantiteMin_filter') ? $request->request->get('quantiteMin_filter') : "";
            $this->permanence_filter = $request->request->has('permanence_filter') ? $request->request->get('permanence_filter') : "";
            $this->restreint_filter = $request->request->has('restreint_filter') ? $request->request->get('restreint_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $json = array();
            $json['items'] = array();
            $iTotalRecords = count($remisesEffectues);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $remises = $this->handleResults($remises, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            //filtre
            /** @var Remise $remise */
            foreach ($remises as $remise) {
                array_push($json['items'], array(
                    'id' => $remise->getId(),
                    'code' => $remise->getCode(),
                    'description' => $remise->getDescription(),
                ));
            }
            return $this->json(json_encode($json), 200);
        }
        $session->set('previous_location', $request->getUri());
        return $this->render('APMVenteBundle:remise:index.html.twig', [
                "offre_remises" => $remises,
                "offre" => $offre,
                "boutique" => $boutique,
            ]
        );
    }

    /**
     * @param Offre $offre
     * @param Boutique $boutique
     */
    private function listAndShowSecurity($offre = null, $boutique = null)
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $vendeur = $user;
        $gerant = null;
        $proprietaire = null;
        if ($offre) {//autorise le vendeur de l'offre
            $vendeur = $offre->getVendeur();
        }
        if ($boutique) {//autorise le gerant ou le proprietaire
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
        }
        //si ni vendeur, ni gerant ou proprietaire, autorise l'utilisateur pour ses propres offres
        if ($user !== $vendeur && $user !== $gerant && $proprietaire !== $user) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $remises
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($remises, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($remises === null) return array();

        if ($this->code_filter != null) {
            $remises = $remises->filter(function ($e) {//filtrage select
                /** @var Remise $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->permanence_filter != null) {
            $remises = $remises->filter(function ($e) {//filtrage select
                /** @var Remise $e */
                return $e->getPermanence() === intval($this->permanence_filter);
            });
        }
        if ($this->etat_filter != null) {
            $remises = $remises->filter(function ($e) {//filtrage select
                /** @var Remise $e */
                return $e->getEtat() === intval($this->etat_filter);
            });
        }
        if ($this->nombreUtilisation_filter != null) {
            $remises = $remises->filter(function ($e) {//filtrage select
                /** @var Remise $e */
                return $e->getNombreUtilisation() === intval($this->nombreUtilisation_filter);
            });
        }
        if ($this->valeurMin_filter != null) {
            $remises = $remises->filter(function ($e) {//filtrage select
                /** @var Remise $e */
                return $e->getValeur() >= intval($this->valeurMin_filter);
            });
        }
        if ($this->valeurMax_filter != null) {
            $remises = $remises->filter(function ($e) {//filtrage select
                /** @var Remise $e */
                return $e->getValeur() <= intval($this->valeurMax_filter);
            });
        }

        if ($this->offre_filter != null) {
            $remises = $remises->filter(function ($e) {//filter with the begining of the entering word
                /** @var Remise $e */
                $str1 = $e->getOffre()->getDesignation();
                $str2 = $this->offre_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }

        $remises = ($remises !== null) ? $remises->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $remises, function ($e1, $e2) {
            /**
             * @var Remise $e1
             * @var Remise $e2
             */
            $dt1 = $e1->getDate()->getTimestamp();
            $dt2 = $e2->getDate()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $remises = array_slice($remises, $iDisplayStart, $iDisplayLength, true);

        return $remises;
    }

    /**
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response| JsonResponse
     */
    public function newAction(Request $request, Offre $offre)
    {
        $this->createSecurity($offre);
        /** @var Session $session */
        $session = $request->getSession();
        /** @var Remise $remise */
        $remise = TradeFactory::getTradeProvider('remise');
        $form = $this->createForm('APM\VenteBundle\Form\RemiseType', $remise);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $remise->setOffre($offre);
                $em = $this->getDoctrine()->getManager();
                $em->persist($remise);
                $em->flush();
                if ($request->isXmlHttpRequest()) {
                    $json= array();
                    $json["item"] = array(//prevenir le client
                        "action" => 0,
                    );
                    $session->getFlashBag()->add('success', "<strong> remise créée. réf:" . $remise->getCode() . "</strong><br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                return $this->redirectToRoute('apm_vente_remise_show', array('id' => $remise->getId()));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (RuntimeException $rte) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'opération.</strong><br>L'enregistrement a échoué. bien vouloir réessayer plutard, svp!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        $session->set('previous_location', $request->getUri());
        return $this->render('APMVenteBundle:remise:new.html.twig', array(
            'form' => $form->createView(),
            'remise' => $remise
        ));
    }

    /**
     * @param Offre $offre
     */
    private function createSecurity($offre = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        if ($offre) {
            $vendeur = $offre->getVendeur();
            if ($user !== $vendeur) {
                throw $this->createAccessDeniedException();
            }
        }
    }

    /**
     * Finds and displays a Remise entity.
     * @param Remise $remise
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function showAction(Remise $remise)
    {
        $this->listAndShowSecurity($remise->getOffre());
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $remise->getId(),
                'code' => $remise->getCode(),
                'description' => $remise->getDescription(),
                'offre' => $remise->getOffre()->getId(),
                'date' => $remise->getDate()->format('d-m-Y H:i'),
                'dateExpiration' => $remise->getDateExpiration()->format('d-m-Y H:i'),
                'etat' => $remise->getEtat(),
                'quantiteMin' => $remise->getQuantiteMin(),
                'valeur' => $remise->getValeur(),
                'nombreUtilisation' => $remise->getNombreUtilisation(),
                'restreint' => $remise->getRestreint(),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($remise);
        return $this->render('APMVenteBundle:remise:show.html.twig', array(
            'remise' => $remise,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Remise entity.
     *
     * @param Remise $remise The Remise entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Remise $remise)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_remise_delete', array('id' => $remise->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Remise entity.
     * @param Request $request
     * @param Remise $remise
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Remise $remise)
    {
        $this->editAndDeleteSecurity($remise);
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $json = array();
            $json['item'] = array();
            /** @var Session $session */
            $session = $request->getSession();
            $em = $this->getDoctrine()->getManager();
            $property = $request->request->get('name');
            $value = $request->request->get('value');
            switch ($property) {
                case 'permanence':
                    $remise->setPermanence($value);
                    break;
                case 'dateExpiration':
                    $remise->setDateExpiration($value);
                    break;
                case 'valeur':
                    $remise->setValeur($value);
                    break;
                case 'restreint' :
                    $remise->setRestreint($value);
                    break;
                case 'quantiteMin' :
                    $remise->setQuantiteMin($value);
                    break;
                case 'nombreUtilisation' :
                    $remise->setNombreUtilisation($value);
                    break;
                case 'offre':
                    /** @var Offre $offre */
                    $offre = $em->getRepository('APMVenteBundle:Offre')->find($value);
                    $remise->setOffre($offre);
                    break;
                default:
                    $session->getFlashBag()->add('info', "<strong> Aucune mis à jour effectuée</strong>");
                    return $this->json(json_encode(["item" => null]), 205);
            }
            $em->flush();
            $session->getFlashBag()->add('success', "Mis à jour propriété : <strong>" . $property . "</strong> réf. transaction :" . $remise->getCode() . "<br> Opération effectuée avec succès!");
            return $this->json(json_encode($json), 200);
        }

        $deleteForm = $this->createDeleteForm($remise);
        $editForm = $this->createForm('APM\VenteBundle\Form\RemiseType', $remise);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($remise);
            $em = $this->getDoctrine()->getManager();
            $em->persist($remise);
            $em->flush();

            return $this->redirectToRoute('apm_vente_remise_show', array('id' => $remise->getId()));
        }

        return $this->render('APMVenteBundle:remise:edit.html.twig', array(
            'remise' => $remise,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Remise $remise
     */
    private function editAndDeleteSecurity($remise)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $user = $this->getUser();
        $vendeur = $remise->getOffre()->getVendeur();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($vendeur !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Deletes a Remise entity.
     * @param Request $request
     * @param Remise $remise
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     */
    public function deleteAction(Request $request, Remise $remise)
    {
        $this->editAndDeleteSecurity($remise);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $em->remove($remise);
            $em->flush();
            $json = array();
            return $this->json($json, 200);
        }
        $form = $this->createDeleteForm($remise);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($remise);
            $em->remove($remise);
            $em->flush();
        }
        return $this->redirectToRoute('apm_vente_remise_index', ['id' => $remise->getOffre()->getId()]);
    }

    public function deleteFromListAction(Remise $remise)
    {
        $this->editAndDeleteSecurity($remise);

        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_vente_remise_index', ['id' => $remise->getOffre()->getId()]);
    }
}
