<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Service_apres_vente;
use APM\AchatBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Transaction_produit;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Service_apres_vente controller.
 * @RouteResource("sav", pluralize=false)
 */
class Service_apres_venteController extends FOSRestController
{
    private $offre_filter;
    private $boutique_filter;
    private $desc_filter;
    private $etat_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $affiliation_filter;
    private $code_filter;
    private $commentaire_filter;
    private $client_filter;

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of service-apres-vente.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="offre_filter", "dataType"="string"},
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="designation_filter", "dataType"="string"},
     *      {"name"="date_from_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="date_to_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="etat_filter", "dataType"="integer", "pattern"="1,2,3,4,5,6,7,8|SELECT"},
     *      {"name"="desc_filter", "dataType"="string", "description"="description"},
     *      {"name"="boutique_filter", "dataType"="string"},
     *      {"name"="affiliation_filter", "dataType"="string"},
     *      {"name"="commentaire_filter", "dataType"="string"},
     *      {"name"="client_filter", "dataType"="string", "pattern"="yannick|USERNAME"},
     *      {"name"="length_filter", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start_filter", "dataType"="integer", "requirement"="\d+"},
     *  },
     *
     * output={
     *   "class"="APM\AchatBundle\Entity\Service_apres_vente",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single or a collection of Service_apres_vente",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "achat"}
     * )
     * @ParamConverter("offre", options={"mapping": {"offre_id":"id"}})
     * @param Request $request
     * @param Boutique $boutique
     * @param Offre $offre
     * @return JsonResponse Liste tous les SAV d'un client
     *
     * @Get("/cget/services", name="s")
     * @Get("/cget/services/boutique/{id}", name="s_boutique", requirements={"id"="boutique_id"})
     * @Get("/cget/services/offre/{offre_id}", name="s_offre", requirements={"offre_id"="\d+"})
     */
    public function getAction(Request $request, Boutique $boutique = null, Offre $offre = null)
    {
        try {
            $this->listAndShowSecurity();
            $services = new ArrayCollection();
            if (null !== $boutique) {
                $this->listAndShowSecurity($boutique, null);
                $offres = $boutique->getOffres();
                /** @var Offre $offre */
                foreach ($offres as $offre) {
                    $service_apres_ventes = $offre->getServiceApresVentes();
                    foreach ($service_apres_ventes as $service) {
                        $services->add($service);
                    }
                }

            } elseif (null !== $offre) {
                $this->listAndShowSecurity(null, $offre);
                $services = $offre->getServiceApresVentes();
            } else {// recupérer les sav de toutes les boutiques de l'utilisateur
                /** @var Utilisateur_avm $user */
                $user = $this->getUser();
                if ($request->query->has("q")
                    && $request->query->get("q") === "boutiques"
                ) {
                    //----------------------------------Boutiques -----------------------------------
                    $boutiques = $user->getBoutiquesGerant();
                    /** @var Boutique $boutique */
                    foreach ($boutiques as $boutique) {
                        $offres = $boutique->getOffres();
                        /** @var Offre $offre */
                        foreach ($offres as $offre) {
                            $service_apres_ventes = $offre->getServiceApresVentes();
                            foreach ($service_apres_ventes as $service) {
                                $services->add($service);
                            }
                        }
                    }
                    $boutiques = $user->getBoutiquesProprietaire();
                    /** @var Boutique $boutique */
                    foreach ($boutiques as $boutique) {
                        $offres = $boutique->getOffres();
                        /** @var Offre $offre */
                        foreach ($offres as $offre) {
                            $service_apres_ventes = $offre->getServiceApresVentes();
                            foreach ($service_apres_ventes as $service) {
                                $services->add($service);
                            }
                        }
                    }
                } else {
                    $service_apres_ventes = $user->getServicesApresVentes();
                    foreach ($service_apres_ventes as $service) {
                        $services->add($service);
                    }
                }
            }
            //filter parameters
            $json = array();
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->offre_filter = $request->query->has('offre_filter') ? $request->query->get('offre_filter') : "";
            $this->boutique_filter = $request->query->has('boutique_filter') ? $request->query->get('boutique_filter') : "";
            $this->desc_filter = $request->query->has('desc_filter') ? $request->query->get('desc_filter') : "";
            $this->dateFrom_filter = $request->query->has('date_from_filter') ? $request->query->get('date_from_filter') : "";
            $this->dateTo_filter = $request->query->has('date_to_filter') ? $request->query->get('date_to_filter') : "";
            $this->etat_filter = $request->query->has('etat_filter') ? $request->query->get('etat_filter') : "";
            $this->affiliation_filter = $request->query->has('affiliation_filter') ? $request->query->get('affiliation_filter') : "";
            $this->commentaire_filter = $request->query->has('commentaire_filter') ? $request->query->get('commentaire_filter') : "";
            $this->client_filter = $request->query->has('client_filter') ? $request->query->get('client_filter') : "";
            $iDisplayLength = intval($request->query->get('length'));
            $iDisplayStart = intval($request->query->get('start'));
            $iTotalRecords = count($services); // counting
            $services = $this->handleResults($services, $iTotalRecords, $iDisplayStart, $iDisplayLength); // filtering
            $iFilteredRecords = count($services);
            //------------------------------------
            $data = $this->get('apm_core.data_serialized')->getFormalData($services, array("owner_list"));
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
     * @param Boutique |null $boutique
     * @param Offre |null $offre
     */
    private
    function listAndShowSecurity($boutique = null, $offre = null)
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

    /**
     * @param Collection $services
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($services, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        if ($services == null) return array();

        if ($this->affiliation_filter === 'P') {
            $services = $services->filter(function ($e) {//filtering per owner
                /** @var Service_apres_vente $e */
                return $e->getOffre()->getBoutique()->getProprietaire() === $this->getUser();
            });
        } elseif ($this->affiliation_filter === 'G') { //filtering per shop keeper
            $services = $services->filter(function ($e) {
                /** @var Service_apres_vente $e */
                return $e->getOffre()->getBoutique()->getGerant() === $this->getUser();
            });
        }
        if ($this->etat_filter != null) {
            $services = $services->filter(function ($e) {//filtrage select
                /** @var Service_apres_vente $e */
                return $e->getEtat() === $this->etat_filter;
            });
        }
        if ($this->dateFrom_filter != null) {
            $services = $services->filter(function ($e) {//start date
                /** @var Service_apres_vente $e */
                $dt1 = (new \DateTime($e->getDateDue()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $services = $services->filter(function ($e) {//end date
                /** @var Service_apres_vente $e */
                $dt = (new \DateTime($e->getDateDue()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->client_filter != null) {
            $services = $services->filter(function ($e) {//filter with the begining of the entering word
                /** @var Service_apres_vente $e */
                $str1 = $e->getClient()->getCode();
                $str2 = $this->client_filter;
                return strcasecmp($str1, $str2) === 0 ? true : false;
            });
        }
        if ($this->boutique_filter != null) {
            $services = $services->filter(function ($e) {//filter with the begining of the entering word
                /** @var Service_apres_vente $e */
                $str1 = $e->getOffre()->getBoutique()->getDesignation();
                $str2 = $this->boutique_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->offre_filter != null) {
            $services = $services->filter(function ($e) {//filter with the begining of the entering word
                /** @var Service_apres_vente $e */
                $str1 = $e->getOffre()->getDesignation();
                $str2 = $this->offre_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->desc_filter != null) {
            $services = $services->filter(function ($e) {//search for occurences in the text
                /** @var Service_apres_vente $e */
                $subject = $e->getDescriptionPanne();
                $pattern = $this->desc_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->commentaire_filter != null) {
            $services = $services->filter(function ($e) {//search for occurences in the text
                /** @var Service_apres_vente $e */
                $subject = $e->getCommentaire();
                $pattern = $this->commentaire_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $services = ($services !== null) ? $services->toArray() : [];
        //------ filtering and paging -----
        usort(//assortment: descending of date -- du plus recent au plus ancient
            $services, function ($e1, $e2) {
            /**
             * @var Service_apres_vente $e1
             * @var Service_apres_vente $e2
             */
            $dt1 = $e1->getDateDue()->getTimestamp();
            $dt2 = $e2->getDateDue()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $services = array_slice($services, $iDisplayStart, $iDisplayLength, true); //slicing, preserve the keys' order

        return $services;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on SAV.",
     * description="Create an object of type Service_apres_vente.",
     * statusCodes={
     *         201="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization", "required"=true, "description"="Authorization token"}
     * },
     * input={
     *    "class"="APM\AchatBundle\Entity\Service_apres_vente",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Service_apres_vente",
     * },
     *  views = {"default", "achat" }
     * )
     *
     * @param Request $request
     * @param Offre $offre
     * @return View | JsonResponse
     *
     * @Post("/new/sav")
     * @Post("/new/sav/offre/{id}", name="_offre", requirements={"id"="offre_id"},)
     */
    public
    function newAction(Request $request, Offre $offre = null)
    {
        try {
            $this->createSecurity($offre);
            /** @var Service_apres_vente $service_apres_vente */
            $service_apres_vente = TradeFactory::getTradeProvider("service_apres_vente");
            $form = $this->createForm('APM\AchatBundle\Form\Service_apres_venteType', $service_apres_vente);
            $$form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            if (null !== $offre) $service_apres_vente->setOffre($offre);
            $this->createSecurity($service_apres_vente->getOffre());
            $service_apres_vente->setClient($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($service_apres_vente);
            $em->flush();

            return $this->routeRedirectView("api_achat_show_sav", ['id' => $service_apres_vente->getId()], Response::HTTP_CREATED);

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
     * @param Offre|null $offre , il s'agit du produit, figurant dans les transaction du client. (acheté par celui-ci)
     */
    private
    function createSecurity($offre = null)
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
            if (null !== $produit_transactions) {
                /** @var Transaction_produit $produit_transaction */
                foreach ($produit_transactions as $produit_transaction) {
                    $produit = $produit_transaction->getProduit();
                    $client = $produit_transaction->getTransaction()->getBeneficiaire();
                    if ($produit === $offre && $client === $this->getUser()) {
                        $clientDeOffre = true;
                        break;
                    }
                }
            }
            if (!$clientDeOffre) throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type Service_apres_vente.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="service_apres_vente_id"}
     * },
     * output={
     *   "class"="APM\AchatBundle\Entity\Service_apres_vente",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_sav_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "achat"}
     * )
     * @param Service_apres_vente $service_apres_vente
     * @return JsonResponse Afficher ls détails d'une SAV
     *
     * @Get("/show/sav/{id}")
     */
    public function showAction(Service_apres_vente $service_apres_vente)
    {
        $this->listAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($service_apres_vente, ["owner_sav_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on service_apres_vente.",
     * description="Update an object of type service_apres_vente.",
     * statusCodes={
     *         200="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization", "required"=true, "description"="Authorization token"}
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="service_apres_vente Id"}
     * },
     * input={
     *    "class"="APM\AchatBundle\Entity\Service_apres_vente",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Service_apres_vente",
     * },
     *
     * views = {"default", "achat" }
     * )
     * @param Request $request
     * @param Service_apres_vente $service_apres_vente
     * @return View |JsonResponse
     *
     * @Put("/edit/sav/{id}")
     */
    public function editAction(Request $request, Service_apres_vente $service_apres_vente)
    {
        try {
            $this->editAndDeleteSecurity($service_apres_vente);
            $form = $this->createForm('APM\AchatBundle\Form\Service_apres_venteType', $service_apres_vente);
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
            return $this->routeRedirectView("api_achat_show_sav", ["id" => $service_apres_vente->getId()], Response::HTTP_OK);
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

    /**
     * @param Service_apres_vente $service_apres_vente
     */
    private
    function editAndDeleteSecurity($service_apres_vente)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $client = null;
        if (null !== $service_apres_vente) {
            $client = $service_apres_vente->getClient();
            $user = $this->getUser();
            if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($client !== $user)) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }


//------------------------ End INDEX ACTION --------------------------------------------

    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type service_apres_vente.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "required"=true, "requirement"="\d+", "description"="service_apres_vente Id"}
     * },
     * parameters = {
     *      {"name"="exec", "required"=true, "dataType"="string", "requirement"="\D+", "description"="needed to check the origin of the request", "format"="exec=go"}
     * },
     * statusCodes={
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "achat"}
     * )
     * @param Request $request
     * @param Service_apres_vente $service_apres_vente
     * @return View | JsonResponse
     *
     * @Delete("/delete/sav/{id}")
     */
    public function deleteAction(Request $request, Service_apres_vente $service_apres_vente)
    {
        try {
            $this->editAndDeleteSecurity($service_apres_vente);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($service_apres_vente);
            $em->flush();

            return $this->routeRedirectView("api_achat_get_savs", [], Response::HTTP_OK);

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

}
