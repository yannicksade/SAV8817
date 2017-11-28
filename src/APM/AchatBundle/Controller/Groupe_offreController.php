<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Groupe_offre;
use APM\AchatBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Groupe_offre controller.
 * Liste les Groupe d'offre crees par l'utilisateur
 * @RouteResource("groupeoffre",  pluralize=false)
 */
class Groupe_offreController extends Controller
{
    private $code_filter;
    private $propriete_filter;
    private $designation_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $dateVigueurFrom_filter;
    private $dateVigueurTo_filter;
    private $description_filter;
    private $createur_filter;
    private $recurrent_filter;


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Get("/cget/collectionoffres", name="s")
     */
    public function getAction(Request $request)
    {
        $this->listAndShowSecurity();

        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $groupe_offres = $user->getGroupesOffres();//liste
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $this->propriete_filter = $request->request->has('propriete_filter') ? $request->request->get('propriete_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->designation_filter = $request->request->has('designation_filter') ? $request->request->get('designation_filter') : "";
            $this->dateFrom_filter = $request->request->has('dateFrom_filter') ? $request->request->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->request->has('dateTo_filter') ? $request->request->get('dateTo_filter') : "";
            $this->dateVigueurFrom_filter = $request->request->has('dateVigueurFrom_filter') ? $request->request->get('dateVigueurFrom_filter') : "";
            $this->dateVigueurTo_filter = $request->request->has('dateVigueurTo_filter') ? $request->request->get('dateVigueurTo_filter') : "";
            $this->description_filter = $request->request->has('description_filter') ? $request->request->get('description_filter') : "";
            $this->createur_filter = $request->request->has('createur_filter') ? $request->request->get('createur_filter') : "";
            $this->recurrent_filter = $request->request->has('recurrent_filter') ? $request->request->get('recurrent_filter') : "";

            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $json = array();
            $json['items'] = array();
            $iTotalRecords = count($groupe_offres);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $groupe_offres = $this->handleResults($groupe_offres, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            /** @var Groupe_offre $groupe_offre */
            foreach ($groupe_offres as $groupe_offre) {
                array_push($json['items'], array(
                    'id' => $groupe_offre->getId(),
                    'code' => $groupe_offre->getCode(),
                    'designation' => $groupe_offre->getDesignation(),
                    'description' => $groupe_offre->getDescription()
                ));
            }
            return $this->json(json_encode($json), 200);
        }
        return $this->render('APMAchatBundle:groupe_offre:index.html.twig', array(
            'groupe_offres' => $groupe_offres,
            'form' => $form->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    private function listAndShowSecurity(Groupe_offre $groupe_offre = null)
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        if ($groupe_offre !== null) {
            if ($this->getUser() !== $groupe_offre->getCreateur()) {
                throw $this->createAccessDeniedException();
            }
        }
        //------------------------------------------------------------------------------
    }

    /**
     * @param Collection $groupe_offres
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($groupe_offres, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($groupe_offres === null) return array();

        if ($this->code_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//filtrage select
                /** @var Groupe_offre $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->propriete_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//filtrage select
                /** @var Groupe_offre $e */
                return $e->getPropriete() === intval($this->propriete_filter);
            });
        }
        if ($this->recurrent_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//filtrage select
                /** @var Groupe_offre $e */
                return $e->getRecurrent() === boolval($this->recurrent_filter);
            });
        }
        if ($this->createur_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//filter with the begining of the entering word
                /** @var Groupe_offre $e */
                $str1 = $e->getCreateur()->getCode();
                $str2 = $this->createur_filter;
                return strcasecmp($str1, $str2) === 0 ? true : false;
            });
        }
        if ($this->description_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//search for occurences in the text
                /** @var Groupe_offre $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->designation_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//search for occurences in the text
                /** @var Groupe_offre $e */
                $subject = $e->getDesignation();
                $pattern = $this->designation_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->dateFrom_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//start date
                /** @var Groupe_offre $e */
                $dt1 = (new \DateTime($e->getDateCreation()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//end date
                /** @var Groupe_offre $e */
                $dt = (new \DateTime($e->getDateCreation()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->dateVigueurFrom_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//start date
                /** @var Groupe_offre $e */
                $dt1 = (new \DateTime($e->getDateDeVigueur()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateVigueurFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateVigueurTo_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//end date
                /** @var Groupe_offre $e */
                $dt = (new \DateTime($e->getDateDeVigueur()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateVigueurTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }

        $groupe_offres = ($groupe_offres !== null) ? $groupe_offres->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $groupe_offres, function ($e1, $e2) {
            /**
             * @var Groupe_offre $e1
             * @var Groupe_offre $e2
             */
            $dt1 = $e1->getDateDeVigueur()->getTimestamp();
            $dt2 = $e2->getDateDeVigueur()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $groupe_offres = array_slice($groupe_offres, $iDisplayStart, $iDisplayLength, true);

        return $groupe_offres;
    }

    /**
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Post("/new/collectionoffre")
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Session $session */
        $session = $request->getSession();
        /** @var Groupe_offre $groupe_offre */
        $groupe_offre = TradeFactory::getTradeProvider("groupe_offre");
        $form = $this->createForm('APM\AchatBundle\Form\Groupe_offreType', $groupe_offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { //save
            $groupe_offre->setCreateur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            try {
                $em->persist($groupe_offre);
                $em->flush();
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json['item'] = array();
                    $session->getFlashBag()->add('success', "Enregistrement du groupe d'offres : " . "<strong>" . $groupe_offre . "</strong><br>Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                $session->getFlashBag()->add('success', "Enregistrement du groupe d'offres : " . "<strong>" . $groupe_offre . "</strong><br>Opération effectuée avec succès!");
                return $this->redirectToRoute('apm_achat_groupe_show', ['id' => $groupe_offre->getId()]);
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "Echec de l'enregistrement: " . "<strong>" . $groupe_offre . "</strong><br>La création ou la modification du groupe d'offre a échoué!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        return $this->render('APMAchatBundle:groupe_offre:new.html.twig', array(
            'form' => $form->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    private function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        /* ensure that the user is logged in
        */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Request $request
     * @param Groupe_offre $groupe_offre
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @Patch("/edit/collectionoffre/{id}")
     */
    public function editAction(Request $request, Groupe_offre $groupe_offre)
    {
        $this->editAndDeleteSecurity($groupe_offre);
        /** @var Session $session */
        $session = $request->getSession();
        $editForm = $this->createForm('APM\AchatBundle\Form\Groupe_offreType', $groupe_offre);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()
            || $request->isXmlHttpRequest() && $request->isMethod('POST')
        ) {
            $em = $this->getDoctrine()->getManager();
            if ($request->isXmlHttpRequest()) {
                $json = array();
                $json['item'] = array();
                $property = $request->request->get('name');
                $value = $request->request->get('value');
                switch ($property) {
                    case 'dateDeVigueur':
                        $groupe_offre->setDateDeVigueur($value);
                        break;
                    case 'propriete':
                        $groupe_offre->setPropriete($value);
                        break;
                    case 'description':
                        $groupe_offre->setDescription($value);
                        break;
                    case 'designation' :
                        $groupe_offre->setDesignation($value);
                        break;
                    case 'recurrent' :
                        $groupe_offre->setRecurrent($value);
                        break;
                    default:
                        $session->getFlashBag()->add('info', "<strong> Aucune mis à jour effectuée</strong>");
                        return $this->json(json_encode(["item" => null]), 205);
                }
                $em->flush();
                $session->getFlashBag()->add('success', "Mise à jour propriété : <strong>" . $property . "</strong> réf. Groupe d'offre :" . $groupe_offre->getCode() . "<br> Opération effectuée avec succès!");
                return $this->json(json_encode($json), 200);
            }
            $em->flush();
        }
        $deleteForm = $this->createDeleteForm($groupe_offre);
        return $this->render('APMAchatBundle:groupe_offre:edit.html.twig', array(
            'groupe_offre' => $groupe_offre,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    /**
     * @param Groupe_offre $groupe_offre
     */
    private function editAndDeleteSecurity($groupe_offre)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($groupe_offre) {
            $user = $this->getUser();
            if ($groupe_offre->getCreateur() !== $user) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * @param Groupe_offre $groupe_offre
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Groupe_offre $groupe_offre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_achat_groupe_delete', array('id' => $groupe_offre->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Request $request
     * @param Groupe_offre $groupe_offre
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @Get("/show/collectionoffre/{id}")
     */
    public function showAction(Request $request, Groupe_offre $groupe_offre)
    {
        $this->listAndShowSecurity();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $groupe_offre->getId(),
                'code' => $groupe_offre->getCode(),
                'designation' => $groupe_offre->getDesignation(),
                'description' => $groupe_offre->getDescription(),
                'dateDeVigueur' => $groupe_offre->getDateDeVigueur()->format('d-m-Y H:i'),
                'date' => $groupe_offre->getDateCreation()->format('d-m-Y H:i'),
                'recurrent' => $groupe_offre->getRecurrent(),
                'createur' => $groupe_offre->getCreateur()->getId(),
                'propriete' => $groupe_offre->getPropriete(),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($groupe_offre);
        return $this->render('APMVenteBundle:groupe_offre:show.html.twig', array(
            'boutique' => $groupe_offre,
            'form' => $deleteForm->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    /**
     * @param Request $request
     * @param Groupe_offre $groupe_offre
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Delete("/delete/collectionoffre/{id}")
     */
    public function deleteAction(Request $request, Groupe_offre $groupe_offre)
    {
        $this->editAndDeleteSecurity($groupe_offre);
        /** @var Session $session */
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        try {
            $em->remove($groupe_offre);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                $json = array();
                $json['item'];
                return $this->json(json_encode($json), 200);
            }
            $session->getFlashBag()->add('success', "Suppression du groupe d'offres: " . "<strong>" . $groupe_offre . "</strong><br>Opération effectuée avec succès!");
            return $this->redirectToRoute('apm_achat_groupe_index');
        } catch (ConstraintViolationException $cve) {
            $session->getFlashBag()->add('danger', "Echec de la suppression de: " . "<strong>" . $groupe_offre . "</strong><br>L'opération de suppression du groupe d'offre a échoué!");
            return $this->redirectToRoute('apm_achat_groupe_index');
        } catch (AccessDeniedException $ads) {
            $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
            return $this->json(json_encode(["item" => null]));
        }
    }
}
