<?php

namespace APM\VenteBundle\Controller;

use APM\AchatBundle\Entity\Groupe_offre;
use APM\UserBundle\Entity\Admin;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Transaction;
use APM\VenteBundle\Entity\Transaction_produit;
use APM\VenteBundle\Form\OffreType;
use Doctrine\Common\Collections\Collection;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Categorie;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Validator\ValidatorTypeGuesser;

/**
 * Offre controller.
 * @RouteResource("offre", pluralize=false)
 */
class OffreController extends FOSRestController implements ClassResourceInterface
{
    /********************************************AJAX REQUEST********************************************/
    private
        $designation_filter;
    private
        $boutique_filter;
    private
        $code_filter;
    private
        $etat_filter;
    private
        $dateFrom_filter;
    private
        $dateTo_filter;

    /**
     * @ParamConverter("categorie", options={"mapping": {"categorie_id":"id"}})
     * @ParamConverter("user", options={"mapping": {"user_id":"id"}})
     * @ParamConverter("groupe_offre", options={"mapping": {"groupe_id":"id"}})
     * @ParamConverter("transaction", options={"mapping": {"transaction_id":"id"}})
     * Liste les offres de la boutique ou du vendeur
     * @param Request $request
     * @param Boutique $boutique
     * @param Categorie $categorie
     * @param Utilisateur_avm $user
     * @param Groupe_offre|null $groupe_offre
     * @param Transaction|null $transaction
     * @return JsonResponse
     *
     * @Get("/cget/offres", name="s")
     * @Get("/cget/offres/boutique/{id}", name="s_boutique", requirements={"id"="boutique_id"})
     * @Get("/cget/offres/boutique/{id}/categorie/{categorie_id}", name="s_categorie", requirements={"id"="boutique_id", "categorie_id"="\d+"})
     * @Get("/cget/offres/user/{user_id}", name="s_ByAdmin", requirements={"user_id"="\d+"})
     * @Get("/cget/offres/groupe/{groupe_id}", name="s_groupeOffre", requirements={"groupe_offre_id"="\d+"})
     * @Get("/cget/offres/transaction/{transaction_id}", name="s_transaction", requirements={"transaction_id"="\d+"})
     * @Get("/cget/offres/boutique/{id}/transaction/{transaction_id}", name="s_transaction_boutique", requirements={"id"="boutique_id", "transaction_id"="\d+"})
     *
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of offres.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="designation_filter", "dataType"="integer"},
     *      {"name"="date_from_filter",  "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="date_to_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="etat_filter", "dataType"="integer", "pattern"="1,2,3,4,5,6,7,8|SELECT"},
     *      {"name"="length", "dataType"="integer"},
     *      {"name"="start", "dataType"="integer"},
     * },
     * output={
     *   "class"="APM\VenteBundle\Entity\Offre",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     *
     * statusCodes={
     *     "output" = "A single or a collection of offre",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to carry on this operation",
     *     404="Returned when a specified object is not found",
     * },
     *     views={"default","vente"}
     * )
     */
    public function getAction(Request $request, Boutique $boutique = null, Categorie $categorie = null, Utilisateur_avm $user = null, Groupe_offre $groupe_offre = null, Transaction $transaction = null)
    {
        try {
            $vendeur = null;
            $selectedGroup = array("others_list");
            if (null !== $transaction) {
                //security
                $this->listOfrresSecurity($boutique, $transaction);
                $offres = $this->listOffres($transaction);
            } elseif (null !== $boutique) {
                $this->listAndShowSecurity($boutique);
                if ($categorie) {
                    $offres = $categorie->getOffres();
                } else {
                    $offres = $boutique->getOffres();
                }
            } elseif (null !== $groupe_offre) {
                $offres = $groupe_offre->getOffres();
            } else {
                if (null === $user) {
                    $this->listAndShowSecurity();
                    $user = $this->getUser();
                    $selectedGroup = array("owner_list");
                } else {
                    $this->adminSecurity();
                }
                /** @var Collection $offres */
                $offres = $user->getOffres();
            }

            $json = array();
            //filter parameters
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->designation_filter = $request->query->has('desc_filter') ? $request->query->get('desc_filter') : "";
            $this->boutique_filter = $request->query->has('boutique_filter') ? $request->query->get('boutique_filter') : "";
            $this->dateFrom_filter = $request->query->has('date_from_filter') ? $request->query->get('date_from_filter') : "";
            $this->dateTo_filter = $request->query->has('date_to_filter') ? $request->query->get('date_to_filter') : "";
            $this->etat_filter = $request->query->has('etat_filter') ? $request->query->get('etat_filter') : "";
            $iDisplayLength = $request->query->has('length') ? intval($request->query->get('length')) : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            //-----Source -------
            $iTotalRecords = count($offres);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $offres = $this->handleResults($offres, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($offres);
            $data = $this->get('apm_core.data_serialized')->getFormalData($offres, $selectedGroup);
            $json['totalRecords'] = $iTotalRecords;
            $json['filteredRecords'] = $iFilteredRecords; //nbre d'unité
            $json['items'] = $data;
            return new JsonResponse($json, 200);
        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                "status" => 403,
                "message" => $this->get('translator')->trans("Accès refusé", [], 'FOSUserBundle')
            ], Response::HTTP_FORBIDDEN);
        }

    }

    /**
     * @param Boutique|null $boutique
     * @param Transaction $transaction
     */
    private function listOfrresSecurity($boutique, $transaction)
    {
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        $gerant = null;
        $proprietaire = null;
        $auteur = null;
        $beneficiaire = null;
        $user = $this->getUser();
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($beneficiaire) $beneficiaire = $transaction->getBeneficiaire();
        } else {
            $auteur = $transaction->getAuteur();
            $beneficiaire = $transaction->getBeneficiaire();
        }
        if ($user !== $gerant && $user !== $proprietaire && $user !== $auteur && $user !== $beneficiaire) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Transaction $transaction
     * @return Collection
     */
    private function listOffres(Transaction $transaction)
    {
        $offres = new ArrayCollection();
        $transaction_produits = $transaction->getTransactionProduits();
        if (null !== $transaction_produits) {
            /** @var Transaction_produit $transaction_produit */
            foreach ($transaction_produits as $transaction_produit) {
                $offre = $transaction_produit->getProduit();
                $offres->add($offre);
            }
        }
        return $offres;
    }

    /**
     * @param Boutique $boutique
     */
    private function listAndShowSecurity($boutique = null)
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', $user = $this->getUser(), 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    private function adminSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$this->getUser() instanceof Admin) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $offres
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($offres, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($offres === null) return array();

        if ($this->code_filter != null) {
            $offres = $offres->filter(function ($e) {//filtrage select
                /** @var Offre $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->etat_filter != null) {
            $offres = $offres->filter(function ($e) {//filtrage select
                /** @var Offre $e */
                return $e->getEtat() === intval($this->etat_filter);
            });
        }
        if ($this->dateFrom_filter != null) {
            $offres = $offres->filter(function ($e) {//start date
                /** @var Offre $e */
                $dt1 = (new \DateTime($e->getUpdatedAt()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $offres = $offres->filter(function ($e) {//end date
                /** @var Offre $e */
                $dt = (new \DateTime($e->getUpdatedAt()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->boutique_filter != null) {
            $offres = $offres->filter(function ($e) {//filter with the begining of the entering word
                /** @var Offre $e */
                $str1 = $e->getBoutique()->getDesignation();
                $str2 = $this->boutique_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->designation_filter != null) {
            $offres = $offres->filter(function ($e) {//search for occurences in the text
                /** @var Offre $e */
                $subject = $e->getDesignation();
                $pattern = $this->designation_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        $offres = ($offres !== null) ? $offres->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $offres, function ($e1, $e2) {
            /**
             * @var Offre $e1
             * @var Offre $e2
             */
            $dt1 = $e1->getUpdatedAt()->getTimestamp();
            $dt2 = $e2->getUpdatedAt()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $offres = array_slice($offres, $iDisplayStart, $iDisplayLength, true);

        return $offres;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on offre.",
     * description="Create an object of type Offre.",
     * statusCodes={
     *         201="The details are returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization",  "required"=true, "description"="Authorization token"}
     * },
     * authentication= true,
     * authenticationRoles= {
     *          "ROLE_USERAVM"
     *     },
     * input={
     *    "class"="APM\VenteBundle\Form\OffreType",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\FormTypeParser"
     *      }
     * },
     * output={
     *   "class"="APM\VenteBundle\Entity\Offre",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_offre_details", "owner_list"}
     * },
     * parameters= {
     *      {"name"="imagefile1x", "dataType"="integer", "required"= true, "description"="horizontal start point 01"},
     *      {"name"="imagefile1y", "dataType"="integer", "required"= true, "description"="vertical start point 01"},
     *      {"name"="imagefile1w", "dataType"="integer", "required"= true, "description"="width 01"},
     *      {"name"="imagefile1h", "dataType"="integer", "required"= true, "description"="height 01"},
     *
     *      {"name"="imagefile2x", "dataType"="integer", "required"= true, "description"="horizontal start point 02"},
     *      {"name"="imagefile2y", "dataType"="integer", "required"= true, "description"="vertical start point 02"},
     *      {"name"="imagefile2w", "dataType"="integer", "required"= true, "description"="width 02"},
     *      {"name"="imagefile2h", "dataType"="integer", "required"= true, "description"="height 02"},
     *
     *      {"name"="imagefile3x", "dataType"="integer", "required"= true, "description"="horizontal start point 03"},
     *      {"name"="imagefile3y", "dataType"="integer", "required"= true, "description"="vertical start point 03"},
     *      {"name"="imagefile3w", "dataType"="integer", "required"= true, "description"="width 03"},
     *      {"name"="imagefile3h", "dataType"="integer", "required"= true, "description"="height 03"},
     *
     *      {"name"="imagefile4x", "dataType"="integer", "required"= true, "description"="horizontal start point 04"},
     *      {"name"="imagefile4y", "dataType"="integer", "required"= true, "description"="vertical start point 04"},
     *      {"name"="imagefile4w", "dataType"="integer", "required"= true, "description"="width 04"},
     *      {"name"="imagefile4h", "dataType"="integer", "required"= true, "description"="height 04"},
     *
     *  },
     * views = {"default", "vente" }
     * )
     * @param Request $request
     * @return View|JsonResponse
     * @Post("/new/offre")
     */
    public function newAction(Request $request)
    {
        try {
            $this->createSecurity();
            /** @var Offre $offre */
            $offre = TradeFactory::getTradeProvider('offre');
            $form = $this->createForm('APM\VenteBundle\Form\OffreType', $offre);
            $data = $request->request->has($form->getName()) ? $request->request->get($form->getName()) : $data[$form->getName()] = array();
            $form->submit(array_merge($data, $request->files->get($form->getName())));
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $this->createSecurity($offre->getBoutique(), $offre->getCategorie());
            $em = $this->getEM();
            $offre->setVendeur($this->getUser());
            $em->persist($offre);
            $em->flush();
            return $this->routeRedirectView("api_vente_show_offre", ['id' => $offre->getId()], Response::HTTP_CREATED);
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
     * @param Categorie $categorie
     * @param Boutique $boutique
     */
    private function createSecurity($boutique = null, $categorie = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$this->getUser() instanceof Utilisateur_avm) {
            throw $this->createAccessDeniedException();
        }
        if ($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
            if ($categorie) {//Inserer l'offre uniquement dans la meme boutique que la categorie
                $currentBoutique = $categorie->getBoutique();
                if ($currentBoutique !== $boutique) {
                    throw $this->createAccessDeniedException();
                }
            }
        }
        //----------------------------------------------------------------------------------------
    }

    private function getEM()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type Offre.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="offre id"}
     * },
     * output={
     *   "class"="APM\VenteBundle\Entity\Offre",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_offre_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "vente"}
     * )
     * @param Offre $offre
     * @return JsonResponse
     *
     * @Get("/show/offre/{id}")
     */
    public function showAction(Offre $offre)
    {
        $this->listAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($offre, ["owner_offre_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Offre",
     * description="Update an object of type Offre.",
     * statusCodes={
     *         "output"="PUT or POST method can be used",
     *         200="The details are returned only on POST when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"}
     * },
     * authentication= true,
     * authenticationRoles= {
     *          "ROLE_USERAVM"
     *     },
     * input={
     *    "class"="APM\VenteBundle\Form\OffreType",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\FormTypeParser"
     *      }
     * },
     * output={
     *   "class"="APM\VenteBundle\Entity\Offre",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_offre_details", "owner_list"}
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="offre Id"}
     * },
     * parameters= {
     *      {"name"="imagefile1x", "dataType"="integer", "required"= true, "description"="horizontal start point 01"},
     *      {"name"="imagefile1y", "dataType"="integer", "required"= true, "description"="vertical start point 01"},
     *      {"name"="imagefile1w", "dataType"="integer", "required"= true, "description"="width 01"},
     *      {"name"="imagefile1h", "dataType"="integer", "required"= true, "description"="height 01"},
     *
     *      {"name"="imagefile2x", "dataType"="integer", "required"= true, "description"="horizontal start point 02"},
     *      {"name"="imagefile2y", "dataType"="integer", "required"= true, "description"="vertical start point 02"},
     *      {"name"="imagefile2w", "dataType"="integer", "required"= true, "description"="width 02"},
     *      {"name"="imagefile2h", "dataType"="integer", "required"= true, "description"="height 02"},
     *
     *      {"name"="imagefile3x", "dataType"="integer", "required"= true, "description"="horizontal start point 03"},
     *      {"name"="imagefile3y", "dataType"="integer", "required"= true, "description"="vertical start point 03"},
     *      {"name"="imagefile3w", "dataType"="integer", "required"= true, "description"="width 03"},
     *      {"name"="imagefile3h", "dataType"="integer", "required"= true, "description"="height 03"},
     *
     *      {"name"="imagefile4x", "dataType"="integer", "required"= true, "description"="horizontal start point 04"},
     *      {"name"="imagefile4y", "dataType"="integer", "required"= true, "description"="vertical start point 04"},
     *      {"name"="imagefile4w", "dataType"="integer", "required"= true, "description"="width 04"},
     *      {"name"="imagefile4h", "dataType"="integer", "required"= true, "description"="height 04"},
     *  },
     *      views = {"default", "vente" }
     * )
     * @param Request $request
     * @param Offre $offre
     * @return View | JsonResponse
     *
     * @Put("/edit/offre/{id}")
     * @Post("/edit/offre/{id}")
     */
    public function editAction(Request $request, Offre $offre)
    {
        try {
            $this->editAndDeleteSecurity($offre);
            $form = $this->createForm('APM\VenteBundle\Form\OffreType', $offre);
            $data = $request->request->has('offre') ? $request->request->get('offre') : $data['offre'] = array();
            $form->submit(array_merge($data, $request->files->all()['offre']), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getEM();
            $em->flush();
            $response = $request->isMethod('PUT') ? new JsonResponse(['status' => 200], Response::HTTP_OK) : $this->routeRedirectView("api_vente_show_offre", ['id' => $offre->getId()], Response::HTTP_OK);
            return $response;
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
     * @param Offre $offre
     */
    private function editAndDeleteSecurity($offre)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$this->getUser() instanceof Utilisateur_avm) {
            throw $this->createAccessDeniedException();
        }
        if (null !== $offre) {
            $boutique = $offre->getBoutique();
            $user = $this->getUser();
            $proprietaire = null;
            $gerant = null;
            if (null !== $boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
            }
            if ($user !== $offre->getVendeur() && $user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type offre.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="offre Id"}
     * },
     * parameters = {
     *      {"name"="exec", "required"=true, "dataType"="string", "requirement"="\D+", "description"="needed to check the origin of the request", "format"="exec=go"}
     * },
     * statusCodes={
     *     200="Returned when successful",
     *     400="Returned when the data are not valid or an unknown error occurred",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "vente"}
     * )
     * @param Request $request
     * @param Offre $offre
     * @return View|JsonResponse
     * @Delete("/delete/offre/{id}")
     */
    public function deleteAction(Request $request, Offre $offre)
    {
        try {
            $this->editAndDeleteSecurity($offre);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getEM();
            $em->remove($offre);
            $em->flush();
            return new JsonResponse(['status' => 200], Response::HTTP_OK);
        } catch (ConstraintViolationException $cve) {
            return new JsonResponse([
                "status" => 400,
                "message" => $this->get('translator')->trans("impossible de supprimer, vérifiez vos données", [], 'FOSUserBundle')
            ], Response::HTTP_FAILED_DEPENDENCY);
        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }
    }


}
