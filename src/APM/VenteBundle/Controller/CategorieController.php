<?php

namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Categorie;
use APM\VenteBundle\Factory\TradeFactory;
use APM\VenteBundle\Form\CategorieType;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Categorie controller.
 *
 */
class CategorieController extends Controller
{
    private $code_filter;
    private $designation_filter;
    private $description_filter;
    private $livrable_filter;
    private $etat_filter;
    private $categorieCourante_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $publiable_filter;


    /**
     * Liste les catégories par Boutique
     * @param Request $request
     * @param Boutique $boutique
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, Boutique $boutique)
    {
        $this->listAndShowSecurity($boutique);
        $categories = $boutique->getCategories();
        if($request->isXmlHttpRequest()){
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->designation_filter = $request->request->has('designation_filter') ? $request->request->get('designation_filter') : "";
            $this->description_filter = $request->request->has('description_filter') ? $request->request->get('description_filter') : "";
            $this->livrable_filter = $request->request->has('livrable_filter') ? $request->request->get('livrable_filter') : "";
            $this->publiable_filter = $request->request->has('publiable_filter') ? $request->request->get('publiable_filter') : "";
            $this->etat_filter = $request->request->has('etat_filter') ? $request->request->get('etat_filter') : "";
            $this->categorieCourante_filter = $request->request->has('categorieCourante_filter') ? $request->request->get('categorieCourante_filter') : "";
            $this->dateFrom_filter = $request->request->has('date_from_filter') ? $request->request->get('date_from_filter') : "";
            $this->dateTo_filter = $request->request->has('date_to_filter') ? $request->request->get('date_to_filter') : "";
            
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $json = array();
            $iTotalRecords = count($categories);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $categories = $this->handleResults($categories, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                /** @var Categorie $category */
                foreach ($categories as $category) {
                    array_push($json, array(
                        'value' => $category->getId(),
                        'text' => $category->getDesignation(),
                    ));
                }
                return $this->json($json, 200);
        }
        
            return $this->render('APMVenteBundle:categorie:index.html.twig', array(
            'categories' => $categories,
            'boutique' => $boutique
        ));
    }

    /**
     * @param Collection $categories
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($categories, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($categories === null) return array();

        if ($this->code_filter != null) {
            $categories = $categories->filter(function ($e) {//filtrage select
                /** @var Categorie $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->livrable_filter != null) {
            $categories = $categories->filter(function ($e) {//filtrage select
                /** @var Categorie $e */
                return $e->getLivrable() === boolval($this->livrable_filter);
            });
        }
        if ($this->publiable_filter != null) {
            $categories = $categories->filter(function ($e) {//filtrage select
                /** @var Categorie $e */
                return $e->getCode() === boolval($this->publiable_filter);
            });
        }
        if ($this->etat_filter != null) {
            $categories = $categories->filter(function ($e) {//filtrage select
                /** @var Categorie $e */
                return $e->getEtat() === $this->etat_filter;
            });
        }
        if ($this->dateFrom_filter != null) {
            $categories = $categories->filter(function ($e) {//start date
                /** @var Categorie $e */
                $dt1 = (new \DateTime($e->getDateCreation()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $categories = $categories->filter(function ($e) {//end date
                /** @var Categorie $e */
                $dt = (new \DateTime($e->getDateCreation()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->categorieCourante_filter != null) {
            $categories = $categories->filter(function ($e) {//filter with the begining of the entering word
                /** @var Categorie $e */
                $str1 = $e->getBoutique()->getDesignation();
                $str2 = $this->categorieCourante_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->designation_filter != null) {
            $categories = $categories->filter(function ($e) {//search for occurences in the text
                /** @var Categorie $e */
                $subject = $e->getDesignation();
                $pattern = $this->designation_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->description_filter != null) {
            $categories = $categories->filter(function ($e) {//search for occurences in the text
                /** @var Categorie $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        $categories = ($categories !== null) ? $categories->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $categories, function ($e1, $e2) {
            /**
             * @var Categorie $e1
             * @var Categorie $e2
             */
            $dt1 = $e1->getDateCreation()->getTimestamp();
            $dt2 = $e2->getDateCreation()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $categories = array_slice($categories, $iDisplayStart, $iDisplayLength, true);

        return $categories;
    }


    /**
     * @param Boutique $boutique
     */
    private function listAndShowSecurity($boutique = null)
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted(['ROLE_BOUTIQUE', 'ROLE_USERAVM'], null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        if ($boutique) {
            $user = $this->getUser();
            $proprietaire = $boutique->getProprietaire();
            $gerant = $boutique->getGerant();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Creates les catégories sont créées uniquement dans les boutiques
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function newAction(Request $request, Boutique $boutique = null)
    {
        $this->createSecurity($boutique);
        /** @var Categorie $categorie */
        $categorie = TradeFactory::getTradeProvider('categorie');
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->createSecurity($boutique, $categorie->getCategorieCourante());
                $categorie->setBoutique($boutique);
                $em = $this->getEM();
                $em->persist($categorie);
                $em->flush();
                if ($request->isXmlHttpRequest()) {
                    $json['item'] = array();
                    $json["item"] = array(//prevenir le client
                        "action" => 0,
                    );
                    $session->getFlashBag()->add('success', "<strong> Mis à jour de la catécorie: réf:" . $categorie->getCode() . "</strong><br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);

                }
                return $this->redirectToRoute('apm_vente_categorie_show', array('id' => $categorie->getId()));
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
        return $this->render('APMVenteBundle:categorie:new.html.twig', array(
            'form' => $form->createView(),
            'boutique' => $boutique
        ));
    }

    /**
     * @param Boutique $boutique
     * @param Categorie $categorieCourante
     */
    private function createSecurity($boutique = null, $categorieCourante = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //Interdire tout utilisateur si ce n'est pas le gerant ou le proprietaire
        if ($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
            if ($categorieCourante) {
                $currentBoutique = $categorieCourante->getBoutique();
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
     * Finds and displays a Categorie entity.
     * @param Request $request
     * @param Categorie $categorie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Categorie $categorie)
    {
        $this->listAndShowSecurity();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $categorie->getId(),
                'code' => $categorie->getCode(),
                'designation' => $categorie->getDesignation(),
                'description' => $categorie->getDescription(),
                'etat' => $categorie->getEtat(),
                'dateCreation' => $categorie->getDateCreation()->format('d-m-Y H:i'),
                'categorieCourante' => $categorie->getCategorieCourante()->getDesignation(),
                'publiable' => $categorie->getPubliable(),
                'livrable' => $categorie->getLivrable(),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($categorie);
        return $this->render('APMVenteBundle:categorie:show.html.twig', array(
            'categorie' => $categorie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Categorie entity.
     *
     * @param Categorie $categorie The Categorie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Categorie $categorie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_categorie_delete', array('id' => $categorie->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Categorie entity.
     * @param Request $request
     * @param Categorie $categorie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Categorie $categorie)
    {
        $this->editAndDeleteSecurity($categorie);
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $json = array();
            $json['item'] = array();
            /** @var Session $session */
            $session = $request->getSession();
            $em = $this->getDoctrine()->getManager();
            $property = $request->request->get('name');
            $value = $request->request->get('value');
            switch ($property) {
                case 'designation':
                    $categorie->setDesignation($value);
                    break;
                case 'description':
                    $categorie->setDescription($value);
                    break;
                case 'livrable':
                    $categorie->setLivrable($value);
                    break;
                case 'publiable':
                    $categorie->setPubliable($value);
                    break;
                case 'etat':
                    $categorie->setEtat($value);
                    break;
                case 'categorieCourante':
                    /** @var Categorie $categorieCourante */
                    $categorieCourante = $em->getRepository('APMVenteBundle:Categorie')->find($value);
                    $categorie->setCategorieCourante($categorieCourante);
                    break;
                default:
                    $session->getFlashBag()->add('info', "<strong> Aucune mis à jour effectuée</strong>");
                    return $this->json(json_encode(["item" => null]), 205);
            }
            $em->flush();
            $session->getFlashBag()->add('success', "Mis à jour propriété : <strong>" . $property . "</strong> réf. catégorie :" . $categorie->getCode() . "<br> Opération effectuée avec succès!");
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($categorie);
        $editForm = $this->createForm(CategorieType::class, $categorie);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($categorie);
            $em = $this->getEM();
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('apm_vente_categorie_show', array('id' => $categorie->getId()));
        }

        return $this->render('APMVenteBundle:categorie:edit.html.twig', array(
            'categorie' => $categorie,
            'delete_form' => $deleteForm->createView(),
            'edit_form' => $editForm->createView()

        ));
    }

    /**
     * @param Categorie $categorie
     */
    private function editAndDeleteSecurity($categorie)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');

        /* ensure that the user is logged in
        *  and that the one is the owner
         * Interdire tout utilisateur si ce n'est pas le gerant ou le proprietaire
        */
        $user = $this->getUser();
        $boutique = $categorie->getBoutique();
        $gerant = $boutique->getGerant();
        $proprietaire = $boutique->getProprietaire();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($gerant !== $user && $user !== $proprietaire)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Deletes a Categorie entity.
     * @param Request $request
     * @param Categorie $categorie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse| JsonResponse
     */
    public function deleteAction(Request $request, Categorie $categorie)
    {
        $this->editAndDeleteSecurity($categorie);
        $em = $this->getEM();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $em->remove($categorie);
            $em->flush();
            return $this->json($json, 200);
        }
        $form = $this->createDeleteForm($categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($categorie);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_categorie_index', ['id' =>$categorie->getBoutique()->getId()]);
    }

    public function deleteFromListAction(Categorie $categorie)
    {
        $this->editAndDeleteSecurity($categorie);

        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_vente_categorie_index', ['id' =>$categorie->getBoutique()->getId()]);
    }
}
