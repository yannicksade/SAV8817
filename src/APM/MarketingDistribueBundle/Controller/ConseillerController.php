<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\ArrayCollection;
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
 * Conseiller controller.
 * @RouteResource("conseiller", pluralize=false)
 */
class ConseillerController extends Controller
{
    private $matricule_filter;
    private $code_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $valeurQuota_filter;
    private $description_filter;
    private $dateCreationReseauFrom_filter;
    private $dateCreationReseauTo_filter;
    private $isConseillerA2;

    /**
     * Liste tous les conseillers
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Get("/conseiller")
     */
    public function getAction()
    {
        $this->listAndShowSecurity();
        $em = $this->getDoctrine()->getManager();
        $conseillers = $em->getRepository('APMMarketingDistribueBundle:conseiller')->findAll();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['items'] = array();
            $this->matricule_filter = $request->request->has('matricule_filter') ? $request->request->get('matricule_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->dateFrom_filter = $request->request->has('dateFrom_filter') ? $request->request->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->request->has('dateTo_filter') ? $request->request->get('dateTo_filter') : "";
            $this->valeurQuota_filter = $request->request->has('valeurQuota_filter') ? $request->request->get('valeurQuota_filter') : "";
            $this->description_filter = $request->request->has('description_filter') ? $request->request->get('description_filter') : "";
            $this->dateCreationReseauFrom_filter = $request->request->has('dateCreationReseauFrom_filter') ? $request->request->get('dateCreationReseauFrom_filter') : "";
            $this->dateCreationReseauTo_filter = $request->request->has('dateCreationReseauTo_filter') ? $request->request->get('dateCreationReseauTo_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $iTotalRecords = count($conseillers);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $_conseillers = new ArrayCollection();
            foreach ($conseillers as $conseiller) {
                $_conseillers->add($conseiller);
            }
            $conseillers = $this->handleResults($_conseillers, $iTotalRecords, $iDisplayStart, $iDisplayLength);

            /** @var Conseiller $conseiller */
            foreach ($conseillers as $conseiller) {
                array_push($json['items'], array(
                        'id' => $conseiller->getId(),
                        'code' => $conseiller->getCode(),
                        'description' => $conseiller->getDescription(),
                    )
                );
            }
            return $this->json(json_encode($json), 200);
        }
        return $this->render('APMMarketingDistribueBundle:conseiller:index.html.twig', array(
            'conseillers' => $conseillers,
        ));
    }

    private function listAndShowSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $conseillers
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($conseillers, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($conseillers === null) return array();

        if ($this->code_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {
                /** @var Conseiller $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->matricule_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {
                /** @var Conseiller $e */
                return $e->getMatricule() === $this->matricule_filter;
            });
        }
        if ($this->isConseillerA2 != null) {
            $conseillers = $conseillers->filter(function ($e) {
                /** @var Conseiller $e */
                return $e->getConseillerA2() === boolval($this->isConseillerA2);
            });
        }

        if ($this->dateFrom_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//start date
                /** @var Conseiller $e */
                $dt1 = (new \DateTime($e->getDateEnregistrement()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//end date
                /** @var Conseiller $e */
                $dt = (new \DateTime($e->getDateEnregistrement()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->dateFrom_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//start date
                /** @var Conseiller $e */
                $dt1 = (new \DateTime($e->getDateCreationReseau()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//end date
                /** @var Conseiller $e */
                $dt = (new \DateTime($e->getDateCreationReseau()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->description_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//search for occurences in the text
                /** @var Conseiller $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        $conseillers = ($conseillers !== null) ? $conseillers->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $conseillers, function ($e1, $e2) {
            /**
             * @var Conseiller $e1
             * @var Conseiller $e2
             */
            $dt1 = $e1->getDateEnregistrement()->getTimestamp();
            $dt2 = $e2->getDateEnregistrement()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });

        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $conseillers = array_slice($conseillers, $iDisplayStart, $iDisplayLength, true);

        return $conseillers;
    }

    /**
     * Creates a new Conseiller entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Post("/new")
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Session $session */
        $session = $request->getSession();
        /** @var Conseiller $conseiller */
        $conseiller = TradeFactory::getTradeProvider("conseiller");
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\ConseillerType', $conseiller);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $conseiller->setUtilisateur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseiller);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                $json = array();
                $json['item'] = array();
                $session->getFlashBag()->add('success', "<strong> conseiller. réf:" . $conseiller->getCode() . "</strong><br> Opération effectuée avec succès!");
                return $this->json(json_encode($json), 200);
            }
            return $this->redirectToRoute('apm_marketing_conseiller_show', array('id' => $conseiller->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:conseiller:new.html.twig', array(
            'conseiller' => $conseiller,
            'form' => $form->createView(),
        ));
    }

    private function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$user->isConseillerA1()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Conseiller entity.
     * @param Request $request
     * @param Conseiller $conseiller
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Get("/show/{id}")
     */
    public function showAction(Request $request, Conseiller $conseiller)
    {
        $this->listAndShowSecurity();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $conseiller->getId(),
                'code' => $conseiller->getCode(),
                'dateEnregistrement' => $conseiller->getDateEnregistrement()->format('d-m-Y H:i'),
                'dateCreationReseau' => $conseiller->getDateCreationReseau()->format('d-m-Y H:i'),
                'description' => $conseiller->getDescription(),
                'isConseillerA2' => $conseiller->getIsConseillerA2(),
                'matricule' => $conseiller->getMatricule(),
                'valeurQuota' => $conseiller->getValeurQuota(),
                'utilisateur' => $conseiller->getUtilisateur()->getId(),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($conseiller);
        $reseau_form = $this->createNewForm();
        return $this->render('APMMarketingDistribueBundle:conseiller:show.html.twig', array(
            'conseiller' => $conseiller,
            'delete_form' => $deleteForm->createView(),
            'reseau_form' => $reseau_form->createView(),
        ));
    }

    /**
     * Creates a form to delete a Conseiller entity.
     *
     * @param Conseiller $conseiller The Conseiller entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Conseiller $conseiller)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_conseiller_delete', array('id' => $conseiller->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function createNewForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_reseau_new'))
            ->setMethod('PUT')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Conseiller entity.
     * @param Request $request
     * @param Conseiller $conseiller
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Get("/edit/conseiller/{id}")
     */
    public function editAction(Request $request, Conseiller $conseiller)
    {
        $this->editAndDeleteSecurity($conseiller);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\ConseillerType', $conseiller);
        $editForm->handleRequest($request);
        /** @var Session $session */
        $session = $request->getSession();
        if ($editForm->isSubmitted() && $editForm->isValid()
            || $request->isXmlHttpRequest() && $request->isMethod('POST')
        ) {
            $em = $this->getDoctrine()->getManager();
            try {
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json['item'] = array();
                    $property = $request->request->get('name');
                    $value = $request->request->get('value');
                    switch ($property) {
                        case 'isConseillerA2':
                            $conseiller->setIsConseillerA2($value);
                            break;
                        case 'description':
                            $conseiller->setDescription($value);
                            break;
                        case 'nombreInstanceReseau':
                            $conseiller->setNombreInstanceReseau($value);
                            break;
                        case 'matricule':
                            $conseiller->setMatricule($value);
                            break;
                        case 'valeurQuota':
                            $conseiller->setValeurQuota($value);
                            break;
                        case 'masterConseiller':
                            /** @var Conseiller $conseiller */
                            $conseiller = $em->getRepository('APMMarketingDistribueBundle:Conseiller')->find($value);
                            $conseiller->setMasterConseiller($conseiller);
                            break;
                        case 'conseillerDroite':
                            /** @var Conseiller $conseiller */
                            $conseiller = $em->getRepository('APMMarketingDistribueBundle:Conseiller')->find($value);
                            $conseiller->setConseillerDroite($conseiller);
                            break;
                        case 'conseillerGauche':
                            /** @var Conseiller $conseiller */
                            $conseiller = $em->getRepository('APMMarketingDistribueBundle:Conseiller')->find($value);
                            $conseiller->setConseillerGauche($conseiller);
                            break;
                        default:
                            $session->getFlashBag()->add('info', "<strong> Aucune mise à jour effectuée</strong>");
                            return $this->json(json_encode(["item" => null]), 205);
                    }
                    $em->flush();
                    $session->getFlashBag()->add('success', "Mise à jour du profile conseiller : <strong>" . $property . "</strong> réf. Conseiller :" . $conseiller->getMatricule() . "<br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                $em->flush();
                return $this->redirectToRoute('apm_marketing_conseiller_show', array('id' => $conseiller->getId()));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        $deleteForm = $this->createDeleteForm($conseiller);
        return $this->render('APMMarketingDistribueBundle:conseiller:edit.html.twig', array(
            'conseiller' => $conseiller,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Conseiller $conseiller
     */
    private function editAndDeleteSecurity($conseiller)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) ||
            ($conseiller->getUtilisateur() !== $user)
        ) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Deletes a Conseiller entity.
     * @param Request $request
     * @param Conseiller $conseiller
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     *
     * @Delete("/delete/profile-conseiller/{id}")
     */
    public function deleteAction(Request $request, Conseiller $conseiller)
    {
        $this->editAndDeleteSecurity($conseiller);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $em->remove($conseiller);
            $em->flush();
            $json = array();
            $json['item'] = array();
            return $this->json(json_encode($json), 200);
        }
        $form = $this->createDeleteForm($conseiller);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($conseiller);
            $em = $this->getDoctrine()->getManager();
            $em->remove($conseiller);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_conseiller_index');
    }

}
