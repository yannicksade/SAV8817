<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Quota;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Quota controller.
 * @RouteResource("commission")
 */
class QuotaController extends Controller
{
    private $valeurQuota_filter;
    private $code_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $libelle_filter;
    private $description_filter;
    private $boutique_filter;
    private $valeurQuotaFrom_filter;
    private $valeurQuotaTo_filter;


    /**
     *
     *  Liste les commissions de la boutique
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Get("/commissions/boutique/{id}", name="s_boutique")
     */
    public function getAction(Request $request, Boutique $boutique)
    {
        $this->listAndShowSecurity($boutique);
        $quotas = $boutique->getCommissionnements();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['items'] = array();
            $this->valeurQuota_filter = $request->request->has('valeurQuota_filter') ? $request->request->get('valeurQuota_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->dateFrom_filter = $request->request->has('dateFrom_filter') ? $request->request->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->request->has('dateTo_filter') ? $request->request->get('dateTo_filter') : "";
            $this->libelle_filter = $request->request->has('libelle_filter') ? $request->request->get('libelle_filter') : "";
            $this->description_filter = $request->request->has('description_filter') ? $request->request->get('description_filter') : "";
            $this->boutique_filter = $request->request->has('boutique_filter') ? $request->request->get('boutique_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;

            $iTotalRecords = count($quotas);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $quotas = $this->handleResults($quotas, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            /** @var Quota $commission */
            foreach ($quotas as $commission) {
                array_push($json['items'], array(
                    'id' => $commission->getId(),
                    'code' => $commission->getCode(),
                    'libelle' => $commission->getLibelleQuota(),
                    'description' => $commission->getDescription(),
                ));
            }
            return $this->json(json_encode($json), 200);
        }
        return $this->render('APMMarketingDistribueBundle:quota:index.html.twig', array(
            'quotas' => $quotas,
            'boutique' => $boutique,
        ));
    }

    /**
     * @param Boutique $boutique
     */
    private function listAndShowSecurity($boutique = null)
    {
        //-----------------------------------security-----------------------------------------------------------
        // Unable to access the controller unless are the owner or you have the CONSEILLER role
        // Le Conseiller et la boutique à le droit de lister tous les quotas
        $this->denyAccessUnlessGranted(['ROLE_CONSEILLER', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $conseiller = $user->getProfileConseiller();
        $gerant = null;
        $proprietaire = null;
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
        }
        if (null === $conseiller && $user !== $gerant && $user !== $proprietaire) throw $this->createAccessDeniedException();

        //------------------------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $commissions
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($commissions, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($commissions === null) return array();

        if ($this->code_filter != null) {
            $commissions = $commissions->filter(function ($e) {//filtrage select
                /** @var Quota $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->boutique_filter != null) {
            $commissions = $commissions->filter(function ($e) {//filtrage select
                /** @var Quota $e */
                return $e->getBoutiqueProprietaire()->getCode() === $this->boutique_filter;
            });
        }
        if ($this->valeurQuotaFrom_filter != null) {
            $commissions = $commissions->filter(function ($e) {//filtrage select
                /** @var Quota $e */
                return $e->getValeurQuota() <= $this->valeurQuotaFrom_filter;
            });
        }
        if ($this->valeurQuotaTo_filter != null) {
            $commissions = $commissions->filter(function ($e) {//filtrage select
                /** @var Quota $e */
                return $e->getValeurQuota() >= $this->valeurQuotaTo_filter;
            });
        }
        if ($this->dateFrom_filter != null) {
            $commissions = $commissions->filter(function ($e) {//start date
                /** @var Quota $e */
                $dt1 = (new \DateTime($e->getDate()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $commissions = $commissions->filter(function ($e) {//end date
                /** @var Quota $e */
                $dt = (new \DateTime($e->getDate()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->libelle_filter != null) {
            $commissions = $commissions->filter(function ($e) {//search for occurences in the text
                /** @var Quota $e */
                $subject = $e->getLibelleQuota();
                $pattern = $this->libelle_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->description_filter != null) {
            $commissions = $commissions->filter(function ($e) {//search for occurences in the text
                /** @var Quota $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $commissions = ($commissions !== null) ? $commissions->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $commissions, function ($e1, $e2) {
            /**
             * @var Quota $e1
             * @var Quota $e2
             */
            $dt1 = $e1->getDate()->getTimestamp();
            $dt2 = $e2->getDate()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });

        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $commissions = array_slice($commissions, $iDisplayStart, $iDisplayLength, true);

        return $commissions;
    }

    /**
     * Creates a new Quota entity.
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Post("/new/commission/boutique/{id}", name="_boutique")
     */
    public function newAction(Request $request, Boutique $boutique)
    {
        $this->createSecurity($boutique);

        /** @var Quota $quotum */
        $quotum = TradeFactory::getTradeProvider("quota");
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\QuotaType', $quotum);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($boutique);
            $quotum->setBoutiqueProprietaire($boutique);
            $em = $this->getDoctrine()->getManager();
            $em->persist($quotum);
            $em->flush();
            if($request->isXmlHttpRequest()){
                $json = array();
                $json['item'] = array();
                return $this->json(json_encode($json),200);
            }
            return $this->redirectToRoute('apm_marketing_quota_show', array('id' => $quotum->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:quota:new.html.twig', array(
            'quotum' => $quotum,
            'form' => $form->createView(),
            'boutique' => $boutique,
        ));
    }

    /**
     * @param Boutique $boutique
     */
    private function createSecurity($boutique = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Quota entity.
     * @param Request $request
     * @param Quota $quotum
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Get("/show/commission/{id}")
     */
    public function showAction(Request $request, Quota $quotum)
    {
        $this->listAndShowSecurity($quotum->getBoutiqueProprietaire());
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $quotum->getId(),
                'code' => $quotum->getCode(),
                'valeurQuota' => $quotum->getValeurQuota(),
                'date' => $quotum->getDate()->format("d/m/Y - H:i"),
                'libelle' => $quotum->getLibelleQuota(),
                'description' => $quotum->getDescription(),
                'boutique' => $quotum->getBoutiqueProprietaire()->getId(),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($quotum);
        return $this->render('APMMarketingDistribueBundle:quota:show.html.twig', array(
            'quotum' => $quotum,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Quota entity.
     *
     * @param Quota $quotum The Quota entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Quota $quotum)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_quota_delete', array('id' => $quotum->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Quota entity.
     * @param Request $request
     * @param Quota $quotum
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response| JsonResponse
     *
     * @Patch("/edit/commission/{id}")
     */
    public function editAction(Request $request, Quota $quotum)
    {
        $this->editAndDeleteSecurity($quotum);
        $deleteForm = $this->createDeleteForm($quotum);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\QuotaType', $quotum);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()
            || $request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $this->editAndDeleteSecurity($quotum);
            $em = $this->getDoctrine()->getManager();
            try {
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json['item'] = array();
                    $property = $request->request->get('name');
                    $value = $request->request->get('value');
                    switch ($property) {
                        case 'description':
                            $quotum->setDescription($value);
                            break;
                        case 'libelle':
                            $quotum->setLibelleQuota($value);
                            break;
                        case 'valeur':
                            $quotum->setValeurQuota($value);
                            break;
                        default:
                            $session->getFlashBag()->add('info', "<strong> Aucune mise à jour effectuée </strong>");
                            return $this->json(json_encode(["item" => null]), 205);
                    }
                    $em->flush();
                    $session->getFlashBag()->add('success', "Modification du commission : <strong>" . $property . "</strong> <br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                $em->flush();
                return $this->redirectToRoute('apm_marketing_quota_show', array('id' => $quotum->getId()));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        return $this->render('APMMarketingDistribueBundle:quota:edit.html.twig', array(
            'quotum' => $quotum,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Quota $quotum
     */
    private function editAndDeleteSecurity($quotum)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to Edit or delete unless you are the owner
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $gerant = $quotum->getBoutiqueProprietaire()->getGerant();
        $proprietaire = $quotum->getBoutiqueProprietaire()->getProprietaire();
        if ($gerant !== $user && $user !== $proprietaire) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Deletes a Quota entity.
     * @param Request $request
     * @param Quota $quotum
     * @return \Symfony\Component\HttpFoundation\RedirectResponse| JsonResponse
     *
     * @delete("/delete/commission/{id}")
     */
    public function deleteAction(Request $request, Quota $quotum)
    {
        $this->editAndDeleteSecurity($quotum);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $em->remove($conseiller_boutique);
            $em->flush();
            $json = array();
            $json['item'] = array();
            return $this->json(json_encode($json), 200);
        }
        $form = $this->createDeleteForm($quotum);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($quotum);
            $em->remove($quotum);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_quota_index', ['id' => $quotum->getBoutiqueProprietaire()->getId()]);
    }
}
