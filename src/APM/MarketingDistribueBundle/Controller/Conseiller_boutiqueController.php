<?php

namespace APM\MarketingDistribueBundle\Controller;


use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\MarketingDistribueBundle\Entity\Conseiller_boutique;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Conseiller_boutique controller.
 * @RouteResource("conseillerboutique")
 */
class Conseiller_boutiqueController extends Controller
{
    private $gainValeur_filter;
    private $code_filter;
    private $dateTo_filter;
    private $dateFrom_filter;
    private $conseiller_filter;
    private $boutique_filter;

    /**
     * operation du conseiller courant
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse Lister les boutique du conseiller
     *
     * Lister les boutique du conseiller
     * @Get("/cget/conseiller-boutiques", name="s")
     * @Get("/cget/conseillers-boutique/{id}", name="s_boutique")
     */
    public function getAction(Request $request, Boutique $boutique = null)
    {
        $this->listAndShowSecurity();
        if (null === $boutique) {
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $boutiques_conseillers = $user->getProfileConseiller()->getConseillerBoutiques();
        } else {
            $boutiques_conseillers = $boutique->getBoutiqueConseillers();
        }

        $json = array();
        $this->gainValeur_filter = $request->query->has('gainValeur_filter') ? $request->query->get('gainValeur_filter') : "";
        $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
        $this->dateFrom_filter = $request->query->has('dateFrom_filter') ? $request->query->get('dateFrom_filter') : "";
        $this->dateTo_filter = $request->query->has('dateTo_filter') ? $request->query->get('dateTo_filter') : "";
        $this->conseiller_filter = $request->query->has('conseiller_filter') ? $request->query->get('conseiller_filter') : "";
        $this->boutique_filter = $request->query->has('boutique_filter') ? $request->query->get('boutique_filter') : "";
        $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
        $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
        $iTotalRecords = count($boutiques_conseillers);
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        $boutiques_conseillers = $this->handleResults($boutiques_conseillers, $iTotalRecords, $iDisplayStart, $iDisplayLength);
        $iFilteredRecords = count($boutiques_conseillers);
        $data = $this->get('apm_core.data_serialized')->getFormalData($boutiques_conseillers, array("owner_list"));
        $json['totalRecords'] = $iTotalRecords;
        $json['filteredRecords'] = $iFilteredRecords;
        $json['items'] = $data;
        return new JsonResponse($json, 200);

    }


    /**
     * @param Conseiller_boutique $conseiller_boutique
     */
    private function listAndShowSecurity($conseiller_boutique = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER or BOUTIQUE role
        $this->denyAccessUnlessGranted(['ROLE_CONSEILLER', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //Pour afficher les details des boutique affiliés
        if (null !== $conseiller_boutique) {
            $user = $this->getUser();
            $conseiller = $conseiller_boutique->getConseiller()->getUtilisateur();
            $proprietaire = $conseiller_boutique->getBoutique()->getProprietaire();
            $gerant = $conseiller_boutique->getBoutique()->getGerant();
            if ($conseiller !== $user && $user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $boutiques_conseillers
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($boutiques_conseillers, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($boutiques_conseillers === null) return array();

        if ($this->conseiller_filter != null) {
            $boutiques_conseillers = $boutiques_conseillers->filter(function ($e) {//filtrage select
                /** @var Conseiller_boutique $e */
                return $e->getConseiller()->getCode() === $this->conseiller_filter;
            });
        }
        if ($this->boutique_filter != null) {
            $boutiques_conseillers = $boutiques_conseillers->filter(function ($e) {//filtrage select
                /** @var Conseiller_boutique $e */
                return $e->getBoutique()->getCode() === $this->boutique_filter;
            });
        }

        if ($this->dateFrom_filter != null) {
            $boutiques_conseillers = $boutiques_conseillers->filter(function ($e) {//start date
                /** @var Conseiller_boutique $e */
                $dt1 = (new \DateTime($e->getDate()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $boutiques_conseillers = $boutiques_conseillers->filter(function ($e) {//end date
                /** @var Conseiller_boutique $e */
                $dt = (new \DateTime($e->getDate()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        $boutiques_conseillers = ($boutiques_conseillers !== null) ? $boutiques_conseillers->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $boutiques_conseillers, function ($e1, $e2) {
            /**
             * @var Conseiller_boutique $e1
             * @var Conseiller_boutique $e2
             */
            $dt1 = $e1->getDate()->getTimestamp();
            $dt2 = $e2->getDate()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });

        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $boutiques_conseillers = array_slice($boutiques_conseillers, $iDisplayStart, $iDisplayLength, true);

        return $boutiques_conseillers;
    }

    /**
     * Creates a new conseiller_boutique entity.
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Post("/new/conseiller-boutique")
     * @Post("/new/conseiller-boutique/{id}", name="_boutique")
     */
    public function newAction(Request $request, Boutique $boutique = null)
    {
        $this->createSecurity($boutique);
        /** @var Conseiller_boutique $conseiller_boutique */
        $conseiller_boutique = TradeFactory::getTradeProvider("conseiller_boutique");
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\Conseiller_boutiqueType', $conseiller_boutique);
        if (null !== $boutique) $form->remove('boutique');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $this->createSecurity($conseiller_boutique->getBoutique());
            if (null !== $boutique) $conseiller_boutique->setBoutique($boutique);
            $conseiller_boutique->setConseiller($user->getProfileConseiller());
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseiller_boutique);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                $json = array();
                $json['item'] = array();
                return $this->json(json_encode($json), 200);
            }
            if (null !== $boutique) return $this->redirectToRoute('apm_marketing_conseiller_boutique_index');
            return $this->redirectToRoute('apm_marketing_conseiller_boutique_show', array('id' => $conseiller_boutique->getId()));
        }
        return $this->render('APMMarketingDistribueBundle:conseiller_boutique:new.html.twig', array(
            'conseiller_boutique' => $conseiller_boutique,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Boutique $boutique
     */
    private function createSecurity($boutique)
    {
        //---------------------------------security-----------------------------------------------
        // Vérifier que l'utilisateur courant est bel et bien le conseiller
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $conseiller = $user->getProfileConseiller();
        $oldConseiller = null;
        if ($boutique && null !== $conseiller) {//l'enregistrement devrait être unique
            $em = $this->getDoctrine()->getManager();
            $oldConseiller = $em->getRepository('APMMarketingDistribueBundle:Conseiller_boutique')
                ->findOneBy(['conseiller' => $conseiller, 'boutique' => $boutique]);
        }

        if (null === $conseiller || null !== $oldConseiller) throw $this->createAccessDeniedException();
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a conseiller_boutique entity.
     * @param Conseiller_boutique $conseiller_boutique
     * @return JsonResponse
     *
     * @Get("/show/conseiller-boutique/{id}")
     */
    public function showAction(Conseiller_boutique $conseiller_boutique)
    {
        $this->listAndShowSecurity($conseiller_boutique);
        $data = $this->get('apm_core.data_serialized')->getFormalData($conseiller_boutique, ["owner_conseillerB_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * Displays a form to edit an existing conseiller_boutique entity.
     * @param Request $request
     * @param Conseiller_boutique $conseiller_boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Patch("/edit/conseiller-boutique/{id}")
     */
    public function editAction(Request $request, Conseiller_boutique $conseiller_boutique)
    {
        $this->editAndDeleteSecurity($conseiller_boutique, $conseiller_boutique->getConseiller());

        $deleteForm = $this->createDeleteForm($conseiller_boutique);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\Conseiller_boutiqueType', $conseiller_boutique);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()
            || $request->isXmlHttpRequest() && $request->isMethod("POST")
        ) {
            try {
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json['item'] = array();
                    $property = $request->query->get('name');
                    $value = $request->query->get('value');
                    switch ($property) {
                        case 'gainValeur':
                            $conseiller_boutique->setGainValeur($value);
                            break;
                        case 'conseiller':
                            /** @var Conseiller $conseiller */
                            $conseiller = $em->getRepository('APMMarketingDistribueBundle:conseiller')->find($value);
                            $conseiller_boutique->setConseiller($conseiller);
                            break;
                        case 'boutique':
                            /** @var Boutique $boutique */
                            $boutique = $em->getRepository('APMVenteBundle:Boutique')->find($value);
                            $conseiller_boutique->setBoutique($boutique);
                            break;
                        default:
                            $session->getFlashBag()->add('info', "<strong> Aucune mise à jour effectuée</strong>");
                            return $this->json(json_encode(["item" => null]), 205);
                    }
                    $em->flush();
                    $session->getFlashBag()->add('success', "Modification du conseiller boutique : <strong>" . $property . "</strong> <br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                $em->flush();
                return $this->redirectToRoute('apm_marketing_conseiller_boutique_show', array('id' => $conseiller_boutique->getId()));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        return $this->render('APMMarketingDistribueBundle:conseiller_boutique:edit.html.twig', array(
            'conseiller_boutique' => $conseiller_boutique,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Conseiller_boutique $conseiller_boutique
     * @param Conseiller $conseiller
     */
    private function editAndDeleteSecurity($conseiller_boutique, $conseiller)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            throw $this->createAccessDeniedException();

        $user = $this->getUser();
        if ($conseiller_boutique) {
            $conseiller = $conseiller_boutique->getConseiller()->getUtilisateur();
        } else {
            $grantedUser = $conseiller->getUtilisateur();
        }

        if ($conseiller !== $user && $user !== $grantedUser) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Creates a form to delete a conseiller_boutique entity.
     *
     * @param Conseiller_boutique $conseiller_boutique The conseiller_boutique entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Conseiller_boutique $conseiller_boutique)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_conseiller_boutique_delete', array('id' => $conseiller_boutique->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Deletes a conseiller_boutique entity.
     * @param Request $request
     * @param Conseiller_boutique $conseiller_boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     *
     * @Delete("/delete/conseiller-boutique/{id}")
     */
    public function deleteAction(Request $request, Conseiller_boutique $conseiller_boutique)
    {
        $this->editAndDeleteSecurity($conseiller_boutique, $conseiller_boutique->getConseiller());
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $em->remove($conseiller_boutique);
            $em->flush();
            $json = array();
            $json['item'] = array();
            return $this->json(json_encode($json), 200);
        }
        $form = $this->createDeleteForm($conseiller_boutique);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($conseiller_boutique, $conseiller_boutique->getConseiller());
            $em->remove($conseiller_boutique);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_conseiller_boutique_index');
    }
}
