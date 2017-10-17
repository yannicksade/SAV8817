<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Commentaire;
use APM\UserBundle\Factory\TradeFactory;
use APM\VenteBundle\Entity\Offre;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Commentaire controller.
 * Tout utilisateur peut éditer, modifier ou supprimer des commentaires sur n'importe qu'elle offre; mais seul le proprietaire
 * de l'offre peut les publier
 *
 */
class CommentaireController extends Controller
{
    private $contenu_filter;
    private $dateLimiteFrom_filter;
    private $dateLimiteTo_filter;
    private $publiable_filter;
    private $utilisateur_filter;
    private $evaluationMin_filter;
    private $evaluationMax_filter;


    /**
     * Liste les commentaires faits sur une offre
     * un commentaire sur une offre pourrait être publié ou non
     * Tant q'un commentaire n'est pas publié, il n'est accessible qu'à celui qui l'a posté et au propriétaire(et gerant) de l'offre
     *
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, Offre $offre)
    {
        $this->listAndShowSecurity();
        $vendeur = $offre->getVendeur();
        $boutique = $offre->getBoutique();
        $user = $this->getUser();
        $gerant = null;
        $proprietaire = null;
        $commentaires = null;
        if (null !== $boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
        }
        $comments = $offre->getCommentaires();
        $commentaires = $comments;
        if ($user !== $vendeur && $user !== $gerant && $user !== $proprietaire) {
            $commentaires = null;
            /** @var Commentaire $commentaire */
            foreach ($comments as $commentaire) { //presenter uniquement les commentaires publiés au publique
                if ($commentaire->isPubliable() || $commentaire->getUtilisateur() === $user) {
                    $commentaires [] = $commentaire;
                }
            }
        }

        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $this->contenu_filter = $request->request->has('contenu_filter') ? $request->request->get('contenu_filter') : "";
            $this->dateLimiteFrom_filter = $request->request->has('dateLimiteFrom_filter') ? $request->request->get('dateLimiteFrom_filter') : "";
            $this->dateLimiteTo_filter = $request->request->has('dateLimiteTo_filter') ? $request->request->get('dateLimiteTo_filter') : "";
            $this->publiable_filter = $request->request->has('publiable_filter') ? $request->request->get('publiable_filter') : "";
            $this->utilisateur_filter = $request->request->has('utilisateur_filter') ? $request->request->get('utilisateur_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $json = array();

            $iTotalRecords = count($commentaires);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $commentaires = $this->handleResults($commentaires, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            //filtre
            /** @var Commentaire offre $commentaire */
            foreach ($commentaires as $commentaire) {
                array_push($json, array(
                    'id' => $commentaire->getId(),
                    'enonceContenu' => substr($commentaire->getContenu(), 10),
                ));
            }
            return $this->json($json, 200);
        }
        return $this->render('APMUserBundle:commentaire:index.html.twig', array(
            'commentaires' => $commentaires,
            'offre' => $offre,
        ));
    }

    /**
     *
     * @internal param bool $access
     */
    private function listAndShowSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     * @param Collection $commentaires
     */
    private function handleResults($commentaires, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($commentaires === null) return array();
        if ($this->evaluationMin_filter != null) {
            $commentaires = $commentaires->filter(function ($e) {//filtrage select
                /** @var Commentaire $e */
                return $e->getEvaluation() >= intval($this->evaluationMin_filter);
            });
        }
        if ($this->evaluationMax_filter != null) {
            $commentaires = $commentaires->filter(function ($e) {//filtrage select
                /** @var Commentaire $e */
                return $e->getEvaluation() <= intval($this->evaluationMax_filter);
            });
        }
        if ($this->dateLimiteFrom_filter != null) {
            $commentaires = $commentaires->filter(function ($e) {//start date
                /** @var Commentaire $e */
                $dt1 = (new \DateTime($e->getDate()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateLimiteFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateLimiteTo_filter != null) {
            $commentaires = $commentaires->filter(function ($e) {//end date
                /** @var Commentaire $e */
                $dt = (new \DateTime($e->getDate()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateLimiteTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }


        if ($this->contenu_filter != null) {
            $commentaires = $commentaires->filter(function ($e) {//search for occurences in the text
                /** @var Commentaire $e */
                $subject = $e->getContenu();
                $pattern = $this->contenu_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $commentaires = ($commentaires !== null) ? $commentaires->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $commentaires, function ($e1, $e2) {
            /**
             * @var Commentaire $e1
             * @var Commentaire $e2
             */
            $dt1 = $e1->getDate()->getTimestamp();
            $dt2 = $e2->getDate()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $commentaires = array_slice($commentaires, $iDisplayStart, $iDisplayLength, true);

        return $commentaires;
    }

    /**
     * Creates a new Commentaire entity.
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function newAction(Request $request, Offre $offre)
    {
        $this->createSecurity($offre);
        /** @var Session $session */
        $session = $request->getSession();
        /** @var Commentaire $commentaire */
        $commentaire = TradeFactory::getTradeProvider("commentaire");
        $form = $this->createForm('APM\UserBundle\Form\CommentaireType', $commentaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->createSecurity($offre);
                $commentaire->setUtilisateur($this->getUser());
                $commentaire->setOffre($offre);
                $em = $this->getDoctrine()->getManager();
                $em->persist($commentaire);
                $em->flush();
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json["item"] = array(//prevenir le client
                        "action" => 0,
                    );
                    $session->getFlashBag()->add('success', "<strong> rabais d'offre créée. réf:" . substr($commentaire->getContenu(), 5) . "</strong><br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                return $this->redirectToRoute('apm_user_commentaire_show', array('id' => $commentaire->getId()));
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
        $session->set('previous_location', $request->getUri());
        return $this->render('APMUserBundle:commentaire:new.html.twig', array(
            'commentaire' => $commentaire,
            'offre' => $offre,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Offre $offre
     */
    private function createSecurity($offre)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if (!$offre->getPubliable()) throw $this->createAccessDeniedException();
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Commentaire entity.
     * @param Request $request
     * @param Commentaire $commentaire
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Commentaire $commentaire)
    {
        $this->listAndShowSecurity();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $commentaire->getId(),
                'date' => $commentaire->getDate()->format('d-m-Y H:i'),
                'contenu' => $commentaire->getContenu(),
                'publiable' => $commentaire->getPubliable(),
                'evaluation' => $commentaire->getEvaluation(),
                'utilisateur' => $commentaire->getUtilisateur()->getId(),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($commentaire);
        return $this->render('APMUserBundle:commentaire:show.html.twig', array(
            'commentaire' => $commentaire,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Commentaire entity.
     *
     * @param Commentaire $commentaire The Commentaire entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Commentaire $commentaire)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_user_commentaire_delete', array('id' => $commentaire->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Commentaire entity.
     * @param Request $request
     * @param Commentaire $commentaire
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Commentaire $commentaire)
    {
        $this->editAndDeleteSecurity($commentaire);
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $json = array();
            $json['item'] = array();
            /** @var Session $session */
            $session = $request->getSession();
            $em = $this->getDoctrine()->getManager();
            $property = $request->request->get('name');
            $value = $request->request->get('value');
            switch ($property) {
                case 'publiable':
                    $commentaire->setPubliable($value);
                    break;
                case 'evaluation' :
                    $commentaire->setEvaluation($value);
                    break;
                default:
                    $session->getFlashBag()->add('info', "<strong> Aucune mis à jour effectuée</strong>");
                    return $this->json(json_encode(["item" => null]), 205);
            }
            $em->flush();
            $session->getFlashBag()->add('success', "Mis à jour propriété : <strong>" . $property . "</strong> réf. commentaire :" . substr($commentaire->getContenu(), 5) . "<br> Opération effectuée avec succès!");
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($commentaire);
        $editForm = $this->createForm('APM\UserBundle\Form\CommentaireType', $commentaire);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($commentaire);
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();

            return $this->redirectToRoute('apm_user_commentaire_show', array('id' => $commentaire->getId()));
        }

        return $this->render('APMUserBundle:commentaire:edit.html.twig', array(
            'commentaire' => $commentaire,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Commentaire $commentaire
     */
    private function editAndDeleteSecurity($commentaire)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        /* ensure that the user is logged in
        *  and that the one is the author
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($commentaire->getUtilisateur() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    // pour soumettre un commentaire il faut que l'offre soit publique

    /**
     * Deletes a Commentaire entity.
     * @param Request $request
     * @param Commentaire $commentaire
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     */
    public function deleteAction(Request $request, Commentaire $commentaire)
    {
        $this->editAndDeleteSecurity($commentaire);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $em->remove($commentaire);
            $em->flush();
            $json = array();
            return $this->json($json, 200);
        }
        $form = $this->createDeleteForm($commentaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($commentaire);
            $em->remove($commentaire);
            $em->flush();
        }
        return $this->redirectToRoute('apm_user_commentaire_index');
    }

    public function deleteFromListAction(Commentaire $commentaire)
    {
        $this->editAndDeleteSecurity($commentaire);
        $em = $this->getDoctrine()->getManager();
        $em->remove($commentaire);
        $em->flush();

        return $this->redirectToRoute('apm_user_commentaire_index');
    }
}
