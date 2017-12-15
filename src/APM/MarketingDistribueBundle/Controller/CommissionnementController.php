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
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Commissionnement controller.
 * @RouteResource("commissionnement", pluralize=false)
 */
class CommissionnementController extends FOSRestController
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
     *
     * @Get("/cget/commissionnements", name="s")
     * @Get("/cget/commissionnements/boutique/{id}", name="s_boutique")
     *
     */
    public function getAction(Request $request, Boutique $boutique = null)
    {
        try {
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

            $json = array();
            $this->creditDepenseFrom_filter = $request->query->has('creditDepenseFrom_filter') ? $request->query->get('creditDepenseFrom_filter') : "";
            $this->creditDepenseTo_filter = $request->query->has('creditDepenseTo_filter') ? $request->query->get('creditDepenseTo_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->dateFrom_filter = $request->query->has('dateFrom_filter') ? $request->query->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->query->has('dateTo_filter') ? $request->query->get('dateTo_filter') : "";
            $this->libelle_filter = $request->query->has('libelle_filter') ? $request->query->get('libelle_filter') : "";
            $this->description_filter = $request->query->has('description_filter') ? $request->query->get('description_filter') : "";
            $this->quantiteFrom_filter = $request->query->has('quantiteFrom_filter') ? $request->query->get('quantiteFrom_filter') : "";
            $this->quantiteTo_filter = $request->query->has('quantiteTo_filter') ? $request->query->get('quantiteTo_filter') : "";
            $this->conseiller_filter = $request->query->has('conseiller_filter') ? $request->query->get('conseiller_filter') : "";
            $this->commission_filter = $request->query->has('commission_filter') ? $request->query->get('commission_filter') : "";

            $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;

            $boutiques_commissionnements = new ArrayCollection($boutiques_commissionnements);
            $iTotalRecords = count($boutiques_commissionnements);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $boutiques_commissionnements = $this->handleResults($boutiques_commissionnements, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($boutiques_commissionnements);
            $data = $this->get('apm_core.data_serialized')->getFormalData($boutiques_commissionnements, array("owner_list"));
            $json['totalRecords'] = $iTotalRecords;
            $json['filteredRecords'] = $iFilteredRecords;
            $json['items'] = $data;

            return new JsonResponse($json, 200);

        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }
    }

    /**
     * @param Commissionnement |null $commissionnement
     * @param Boutique $boutique
     */
    private
    function listAndShowSecurity($commissionnement = null, $boutique = null)
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
     * @param Collection $commissionnements
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private
    function handleResults($commissionnements, $iTotalRecords, $iDisplayStart, $iDisplayLength)
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
     * @param Request $request
     * @param Boutique $boutique
     * @return View | JsonResponse
     *
     *
     * @Post("/new/commissionnement/boutique/{id}")
     */
    public
    function newAction(Request $request, Boutique $boutique)
    {
        try {
            $this->createSecurity($boutique);
            /** @var Commissionnement $commissionnement */
            $commissionnement = TradeFactory::getTradeProvider("commissionnement");
            $form = $this->createForm('APM\MarketingDistribueBundle\Form\CommissionnementType', $commissionnement);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $this->createSecurity($boutique, $commissionnement->getCommission());
            $em = $this->getDoctrine()->getManager();
            $em->persist($commissionnement);
            $em->flush();

            return $this->routeRedirectView("api_marketing_show_commissionnement", ['id' => $commissionnement->getId()], Response::HTTP_CREATED);

        } catch (ConstraintViolationException $cve) {
            return new JsonResponse([
                "status" => 400,
                "message" => $this->get('translator')->trans("impossible d'enregistrer, vérifiez vos données", [], 'FOSUserBundle')
            ], Response::HTTP_BAD_REQUEST);
        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }
    }

    /**
     * @param Boutique $boutique
     * @param Quota $quota
     */
    private
    function createSecurity($boutique, $quota = null)
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
     * @param Commissionnement $commissionnement
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Get("/show/commissionnement/{id}")
     */
    public
    function showAction(Commissionnement $commissionnement)
    {
        $this->listAndShowSecurity($commissionnement);
        $data = $this->get('apm_core.data_serialized')->getFormalData($transporteur_zoneintervention, ["owner_commissionnement_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * Displays a form to edit an existing Commissionnement entity.
     * @param Request $request
     * @param Commissionnement $commissionnement
     * @return View | JsonResponse
     *
     * @Patch("/edit/commissionnement/{id}")
     */
    public
    function editAction(Request $request, Commissionnement $commissionnement)
    {
        try {
            $this->editSecurity();
            $form = $this->createForm('APM\MarketingDistribueBundle\Form\CommissionnementType', $commissionnement);
            $form->submit($request->request->all(), false);
            if (!$form->isValid()) {
                return new JsonResponse(
                    [
                        "status" => 400,
                        "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                    ], Response::HTTP_BAD_REQUEST
                );
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->routeRedirectView("api_marketing_show_commissionnement", ['id' => $commissionnement->getId()], Response::HTTP_OK);

        } catch (ConstraintViolationException $cve) {
            return new JsonResponse(
                [
                    "status" => 400,
                    "message" => $this->get('translator')->trans("impossible d'enregistrer, vérifiez vos données", [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST
            );
        } catch (AccessDeniedException $ads) {
            return new JsonResponse(
                [
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }
    }

    private
    function editSecurity()
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
     * @return View | JsonResponse
     *
     * @Delete("/delete/commissionnement/{id}")
     */
    public
    function deleteAction(Request $request, Commissionnement $commissionnement)
    {
        try {
            $this->deleteSecurity($commissionnement);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($commissionnement);
            $em->flush();
            return $this->routeRedirectView("api_marketing_get_commissionnements", [], Response::HTTP_OK);
        } catch (ConstraintViolationException $cve) {
            return new JsonResponse(
                [
                    "status" => 400,
                    "message" => $this->get('translator')->trans("impossible de supprimer, vérifiez vos données", [], 'FOSUserBundle')
                ], Response::HTTP_FAILED_DEPENDENCY
            );
        } catch (AccessDeniedException $ads) {
            return new JsonResponse(
                [
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }
    }

//-------------------------------------------------------

    /**
     * Le conseiller peut supprimer ses commissionnement
     * @param Commissionnement $commissionnement
     */
    private
    function deleteSecurity($commissionnement)
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

}

