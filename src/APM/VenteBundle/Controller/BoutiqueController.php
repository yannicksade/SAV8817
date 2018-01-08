<?php

namespace APM\VenteBundle\Controller;

use APM\UserBundle\Entity\Admin;
use APM\UserBundle\Entity\Utilisateur;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Boutique controller.
 * @RouteResource("boutique", pluralize=false)
 */
class BoutiqueController extends FOSRestController implements ClassResourceInterface
{
    private $designation_filter;
    private $code_filter;
    private $etat_filter;
    private $nationalite_filter;
    private $description_filter;
    private $dateCreationFrom_filter;
    private $dateCreationTo_filter;
    #private $dateTo_filter;
    #private $dateFrom_filter;

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of boutiques.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="nationalite_filter", "dataType"="string"},
     *      {"name"="dateCreationFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateCreationTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="description_filter", "dataType"="string"},
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="etat_filter", "dataType"="integer"},
     *      {"name"="designation_filter", "dataType"="string"},
     *      {"name"="length", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start", "dataType"="integer", "requirement"="\d+"},
     *  },
     * output={
     *   "class"="APM\VenteBundle\Entity\Boutique",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     *  parameters= {
     *      {"name"="q", "required"=false, "dataType"="string", "description"="OWNER|SHOPKEEPER", "format"= "?q=owner"}
     *  },
     * statusCodes={
     *     "output" = "A single or a collection of boutiques",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "vente"}
     * )
     * @param Request $request
     * @param Utilisateur_avm|null $user
     * @return JsonResponse
     *
     * @Get("/cget/boutiques", name="s")
     * @Get("/cget/boutiques/user/{id}", name="s_user", requirements={"id"="user_id"})
     */
    public function getAction(Request $request, Utilisateur_avm $user = null)
    {
        try {
            $this->personalSecurity();
            /** @var Utilisateur_avm $user */
            if (null === $user) {
                $user = $this->getUser();
            } else {
                $this->adminSecurity();
            }
            /** @var Boutique $boutique */
            $this->dateCreationFrom_filter = $request->query->has('dateCreationFrom_filter') ? $request->query->get('dateCreationFrom_filter') : "";
            $this->dateCreationTo_filter = $request->query->has('dateCreationTo_filter') ? $request->query->get('dateCreationTo_filter') : "";
            $this->nationalite_filter = $request->query->has('nationalite_filter') ? $request->query->get('nationalite_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->designation_filter = $request->query->has('designation_filter') ? $request->query->get('designation_filter') : "";
            $this->etat_filter = $request->query->has('etat_filter') ? $request->query->get('etat_filter') : "";
            $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
            $iDisplayStart = $request->query->has('start') ? $request->query->get('start') : 0;
            $selectedGroup = array("owner_list");
            $json = array();
            $json['items'] = array();
            $q = $request->query->has('q') ? $request->query->get('q') : "all";
            if ($q === "owner" || $q === "all") {
                $boutiques = $user->getBoutiquesProprietaire();
                $iTotalRecords = count($boutiques);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $boutiques = $this->handleResults($boutiques, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                $iFilteredRecords = count($boutiques);
                $data = $this->get('apm_core.data_serialized')->getFormalData($boutiques, $selectedGroup);
                $json['items']['owner'] = $data;
                $json['totalRecordsOwner'] = $iTotalRecords;
                $json['filteredRecordsOwner'] = $iFilteredRecords;
            }
            if ($q === "shopkeeper" || $q === "all") {
                $boutiquesGerant = $user->getBoutiquesGerant();
                $iTotalRecords = count($boutiquesGerant);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $boutiquesGerant = $this->handleResults($boutiquesGerant, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                $iFilteredRecords = count($boutiquesGerant);
                $data = $this->get('apm_core.data_serialized')->getFormalData($boutiquesGerant, $selectedGroup);
                $json['totalRecordsShopkeeper'] = $iTotalRecords;
                $json['filteredRecordsShopkeeper'] = $iFilteredRecords;
                $json['items']['shopkeeper'] = $data;
            }
            return new JsonResponse($json, 200);
        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                "status" => 403,
                "message" => $this->get('translator')->trans("Accès refusé", [], 'FOSUserBundle')
            ], Response::HTTP_FORBIDDEN);
        }
    }

    private
    function personalSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') || !$this->getUser() instanceof Utilisateur_avm) {
            throw $this->createAccessDeniedException();
        }
    }

    private
    function adminSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_STAFF', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$this->getUser() instanceof Admin) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $boutiques
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($boutiques, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($boutiques === null) return array();

        if ($this->code_filter != null) {
            $boutiques = $boutiques->filter(function ($e) {//filtrage select
                /** @var Boutique $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->etat_filter != null) {
            $boutiques = $boutiques->filter(function ($e) {//filtrage select
                /** @var Boutique $e */
                return $e->getEtat() === intval($this->etat_filter);
            });
        }
        if ($this->nationalite_filter != null) {
            $boutiques = $boutiques->filter(function ($e) {//filter with the begining of the entering word
                /** @var Boutique $e */
                $str1 = $e->getNationalite();
                $str2 = $this->nationalite_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->designation_filter != null) {
            $boutiques = $boutiques->filter(function ($e) {//search for occurences in the text
                /** @var Boutique $e */
                $subject = $e->getDesignation();
                $pattern = $this->designation_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->description_filter != null) {
            $boutiques = $boutiques->filter(function ($e) {//search for occurences in the text
                /** @var Boutique $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        if ($this->dateCreationFrom_filter != null) {
            $boutiques = $boutiques->filter(function ($e) {//start date
                /** @var Boutique $e */
                $dt1 = (new \DateTime($e->getDateCreation()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateCreationFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateCreationTo_filter != null) {
            $boutiques = $boutiques->filter(function ($e) {//end date
                /** @var Boutique $e */
                $dt = (new \DateTime($e->getDateCreation()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateCreationTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }

        $boutiques = ($boutiques !== null) ? $boutiques->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $boutiques, function ($e1, $e2) {
            /**
             * @var Boutique $e1
             * @var Boutique $e2
             */
            $dt1 = $e1->getUpdatedAt()->getTimestamp();
            $dt2 = $e2->getUpdatedAt()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $boutiques = array_slice($boutiques, $iDisplayStart, $iDisplayLength, true);

        return $boutiques;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Boutique.",
     * description="Create an object of type Boutique.",
     * statusCodes={
     *         201="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization",  "required"=true, "description"="Authorization token"}
     * },
     * input={
     *    "class"="APM\VenteBundle\Entity\Boutique",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      }
     * },
     * parameters= {
     *      {"name"="imagefile1[file]", "dataType"="file", "required"= false, "description"="file 01 top"},
     *      {"name"="imagefile1[x]", "dataType"="integer", "required"= true, "description"="horizontal start point 01"},
     *      {"name"="imagefile1[y]", "dataType"="integer", "required"= true, "description"="vertical start point 01"},
     *      {"name"="imagefile1[w]", "dataType"="integer", "required"= true, "description"="width 01"},
     *      {"name"="imagefile1[h]", "dataType"="integer", "required"= true, "description"="height 01"},
     *      {"name"="imagefile2[file]", "dataType"="file", "required"= false, "description"="file 02 bottom"},
     *      {"name"="imagefile2[x]", "dataType"="integer", "required"= true, "description"="horizontal start point 02"},
     *      {"name"="imagefile2[y]", "dataType"="integer", "required"= true, "description"="vertical start point 02"},
     *      {"name"="imagefile2[w]", "dataType"="integer", "required"= true, "description"="width 02"},
     *      {"name"="imagefile2[h]", "dataType"="integer", "required"= true, "description"="height 02"},
     *      {"name"="imagefile3[file]", "dataType"="file", "required"= false, "description"="file 03 left side"},
     *      {"name"="imagefile3[x]", "dataType"="integer", "required"= true, "description"="horizontal start point 03"},
     *      {"name"="imagefile3[y]", "dataType"="integer", "required"= true, "description"="vertical start point 03"},
     *      {"name"="imagefile3[w]", "dataType"="integer", "required"= true, "description"="width 03"},
     *      {"name"="imagefile3[h]", "dataType"="integer", "required"= true, "description"="height 03"},
     *      {"name"="imagefile4[file]", "dataType"="file", "required"= false, "description"="file 04 right side"},
     *      {"name"="imagefile4[x]", "dataType"="integer", "required"= true, "description"="horizontal start point 04"},
     *      {"name"="imagefile4[y]", "dataType"="integer", "required"= true, "description"="vertical start point 04"},
     *      {"name"="imagefile4[w]", "dataType"="integer", "required"= true, "description"="width 04"},
     *      {"name"="imagefile4[h]", "dataType"="integer", "required"= true, "description"="height 04"},
     *  },
     * views = {"default", "vente" }
     * )
     * @param Request $request
     * @Post("/new/boutique")
     * @return View|JsonResponse
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Boutique $boutique */
        $boutique = TradeFactory::getTradeProvider('boutique');
        $form = $this->createForm('APM\VenteBundle\Form\BoutiqueType', $boutique);
        $form->submit(array_merge($request->request->all(), $request->files->all()));
        if (!$form->isValid()) {
            return new JsonResponse([
                "status" => 400,
                "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
            ], Response::HTTP_BAD_REQUEST);
        }
        try {
            $boutique->setProprietaire($this->getUser());
            $em = $this->getEM();
            $em->persist($boutique);
            $em->flush();
            return $this->routeRedirectView("api_vente_show_boutique", ['id' => $boutique->getId()], Response::HTTP_CREATED);
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

    private
    function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$this->getUser() instanceof Utilisateur_avm) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    private
    function getEM()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    public
    function showImageAction(Request $request, Boutique $boutique)
    {
        $this->listAndShowSecurity();
        $form = $this->createCrobForm($boutique);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('apm_core.crop_image')->setCropParameters(intval($_POST['x']), intval($_POST['y']), intval($_POST['w']), intval($_POST['h']), $boutique->getImage(), $boutique);

            return $this->redirectToRoute('apm_vente_boutique_show', array('id' => $boutique->getId()));
        }

        return $this->render('APMVenteBundle:boutique:image.html.twig', array(
            'boutique' => $boutique,
            'crop_form' => $form->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    private
    function listAndShowSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') || !$this->getUser() instanceof Utilisateur_avm) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    private
    function createCrobForm(Boutique $boutique)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_boutique_show-image', array('id' => $boutique->getId())))
            ->setMethod('POST')
            ->getForm();
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type Boutique.",
     * headers={
     *      { "name"="Authorization", "required"=true, "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="boutique id"}
     * },
     * output={
     *   "class"="APM\VenteBundle\Entity\Boutique",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_boutique_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "vente"}
     * )
     * @param Boutique $boutique
     * @return JsonResponse
     *
     * @Get("/show/boutique/{id}")
     */
    public
    function showAction(Boutique $boutique)
    {
        $this->listAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($boutique, ["owner_boutique_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Boutique",
     * description="Update an object of type Boutique.",
     * statusCodes={
     *         200="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"}
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="boutique Id"}
     * },
     * input={
     *    "class"="APM\VenteBundle\Entity\Boutique",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      }
     * },
     * parameters= {
     *      {"name"="imagefile1[file]", "dataType"="file", "required"= false, "description"="file 01 top"},
     *      {"name"="imagefile1[x]", "dataType"="integer", "required"= true, "description"="horizontal start point 01"},
     *      {"name"="imagefile1[y]", "dataType"="integer", "required"= true, "description"="vertical start point 01"},
     *      {"name"="imagefile1[w]", "dataType"="integer", "required"= true, "description"="width 01"},
     *      {"name"="imagefile1[h]", "dataType"="integer", "required"= true, "description"="height 01"},
     *      {"name"="imagefile2[file]", "dataType"="file", "required"= false, "description"="file 02 bottom"},
     *      {"name"="imagefile2[x]", "dataType"="integer", "required"= true, "description"="horizontal start point 02"},
     *      {"name"="imagefile2[y]", "dataType"="integer", "required"= true, "description"="vertical start point 02"},
     *      {"name"="imagefile2[w]", "dataType"="integer", "required"= true, "description"="width 02"},
     *      {"name"="imagefile2[h]", "dataType"="integer", "required"= true, "description"="height 02"},
     *      {"name"="imagefile3[file]", "dataType"="file", "required"= false, "description"="file 03 left side"},
     *      {"name"="imagefile3[x]", "dataType"="integer", "required"= true, "description"="horizontal start point 03"},
     *      {"name"="imagefile3[y]", "dataType"="integer", "required"= true, "description"="vertical start point 03"},
     *      {"name"="imagefile3[w]", "dataType"="integer", "required"= true, "description"="width 03"},
     *      {"name"="imagefile3[h]", "dataType"="integer", "required"= true, "description"="height 03"},
     *      {"name"="imagefile4[file]", "dataType"="file", "required"= false, "description"="file 04 right side"},
     *      {"name"="imagefile4[x]", "dataType"="integer", "required"= true, "description"="horizontal start point 04"},
     *      {"name"="imagefile4[y]", "dataType"="integer", "required"= true, "description"="vertical start point 04"},
     *      {"name"="imagefile4[w]", "dataType"="integer", "required"= true, "description"="width 04"},
     *      {"name"="imagefile4[h]", "dataType"="integer", "required"= true, "description"="height 04"},
     *  },
     * views = {"default", "vente" }
     * )
     * @param Request $request
     * @param Boutique $boutique
     * @return View | JsonResponse
     *
     * @Post("/edit/boutique/{id}")
     */
    public
    function editAction(Request $request, Boutique $boutique)
    {
        try {
            $this->editAndDeleteSecurity($boutique);
            $form = $this->createForm('APM\VenteBundle\Form\BoutiqueType', $boutique);
            $form->submit(array_merge($request->request->all(), $request->files->all()), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            //si le proprietaire change de gerant, il est remplacé dans touts les offres de la boutique
            $oldGerant = $boutique->getGerant();
            $this->personnelBoutique($boutique, $oldGerant, $editForm->get('gerant')->getData());
            $em = $this->getEM();
            $em->flush();
            return $this->routeRedirectView("api_vente_show_boutique", ['id' => $boutique->getId()], Response::HTTP_OK);
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
     */
    private
    function editAndDeleteSecurity($boutique)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || !$user instanceof Utilisateur_avm || ($boutique->getProprietaire() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Boutique $boutique
     * @param $oldGerant
     * @param $newGerant
     */
    private
    function personnelBoutique($boutique, $oldGerant, $newGerant)
    {
        if ($newGerant !== $oldGerant) {
            /** @var Offre $offre */
            foreach ($boutique->getOffres() as $offre) {
                if ($offre->getVendeur() === $oldGerant) {
                    $offre->setVendeur($newGerant);
                }
            }
        }
    }


    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type Boutique.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="boutique Id"}
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
     * @param Boutique $boutique
     * @return JsonResponse| View
     *
     * @Delete("/delete/boutique/{id}")
     */
    public
    function deleteAction(Request $request, Boutique $boutique)
    {
        try {
            $this->editAndDeleteSecurity($boutique);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getEM();
            $em->remove($boutique);
            $em->flush();
            return $this->routeRedirectView("api_vente_get_boutiques", [], Response::HTTP_OK);
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
