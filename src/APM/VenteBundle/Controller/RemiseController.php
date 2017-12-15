<?php

namespace APM\VenteBundle\Controller;

use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Remise;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Remise controller.
 * @RouteResource("remise", pluralize=false)
 */
class RemiseController extends FOSRestController
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

    /**
     * @ParamConverter("offre", options={"mapping": {"offre_id":"id"}})
     * Liste toutes les remises appliquées sur une offre ou les remises créées par un vendeurs
     * @param Request $request
     * @param Boutique|null $boutique
     * @param Offre $offre
     * @return JsonResponse
     *
     * @Get("/cget/remises", name="s")
     * @Get("/cget/remises/boutique/{id}", name="s_boutique")
     * @Get("/cget/remises/{offre_id}", name="s_offre")
     */
    public function getAction(Request $request, Boutique $boutique = null, Offre $offre = null)
    {
        try {
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

            $this->dateExpiration_filter = $request->query->has('dateExpiration_filter') ? $request->query->get('dateExpiration_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->offre_filter = $request->query->has('offre_filter') ? $request->query->get('offre_filter') : "";
            $this->etat_filter = $request->query->has('etat_filter') ? $request->query->get('etat_filter') : "";
            $this->valeurMin_filter = $request->query->has('valeurMin_filter') ? $request->query->get('valeurMin_filter') : "";
            $this->valeurMax_filter = $request->query->has('valeurMax_filter') ? $request->query->get('valeurMax_filter') : "";
            $this->nombreUtilisation_filter = $request->query->has('nombreUtilisation_filter') ? $request->query->get('nombreUtilisation_filter') : "";
            $this->quantiteMin_filter = $request->query->has('quantiteMin_filter') ? $request->query->get('quantiteMin_filter') : "";
            $this->permanence_filter = $request->query->has('permanence_filter') ? $request->query->get('permanence_filter') : "";
            $this->restreint_filter = $request->query->has('restreint_filter') ? $request->query->get('restreint_filter') : "";
            $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            $json = array();
            $iTotalRecords = count($remises);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $remises = $this->handleResults($remises, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            //filtre
            $iFilteredRecords = count($remises);
            $data = $this->get('apm_core.data_serialized')->getFormalData($remises, array("owner_list"));
            $json['totalRecords'] = $iTotalRecords;
            $json['filteredRecords'] = $iFilteredRecords;
            $json['items'] = $data;
            return new JsonResponse($json, 200);
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
     * @return View | JsonResponse
     *
     * @Post("/new/remise/offre/{id}", name="_offre")
     */
    public function newAction(Request $request, Offre $offre)
    {
        try {
            $this->createSecurity($offre);
            /** @var Remise $remise */
            $remise = TradeFactory::getTradeProvider('remise');
            $form = $this->createForm('APM\VenteBundle\Form\RemiseType', $remise);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $remise->setOffre($offre);
            $em = $this->getDoctrine()->getManager();
            $em->persist($remise);
            $em->flush();
            return $this->routeRedirectView("api_vente_show_remise", ['id' => $remise->getId()], Response::HTTP_CREATED);
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
     * @return JsonResponse
     *
     * @Get("/show/remise/{id}")
     */
    public function showAction(Remise $remise)
    {
        $this->listAndShowSecurity($remise->getOffre());
        $data = $this->get('apm_core.data_serialized')->getFormalData($remise, ["owner_remise_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * Displays a form to edit an existing Remise entity.
     * @param Request $request
     * @param Remise $remise
     * @return View | JsonResponse
     *
     * @Put("/edit/remise/{id}")
     */
    public function editAction(Request $request, Remise $remise)
    {
        try {
            $this->editAndDeleteSecurity($remise);
            $form = $this->createForm('APM\VenteBundle\Form\RemiseType', $remise);
            $form->submit($request->request->all(), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->routeRedirectView("api_vente_show_remise", ['id' => $remise->getId()], Response::HTTP_OK);
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
     * @param Request $request
     * @param Remise $remise
     * @return View | JsonResponse
     *
     * @Delete("/delete/remise/{id}")
     */
    public function deleteAction(Request $request, Remise $remise)
    {
        try {
            $this->editAndDeleteSecurity($remise);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $offre = $remise->getOffre();
            $em = $this->getDoctrine()->getManager();
            $em->remove($remise);
            $em->flush();
            return $this->routeRedirectView("api_vente_get_remises_offre", [$offre->getId()], Response::HTTP_OK);
        } catch (ConstraintViolationException $cve) {
            return new JsonResponse(
                [
                    "status" => 400,
                    "message" => $this->get('translator')->trans("impossible de supprimer, vérifiez vos données", [], 'FOSUserBundle')
                ], Response::HTTP_FAILED_DEPENDENCY);
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
