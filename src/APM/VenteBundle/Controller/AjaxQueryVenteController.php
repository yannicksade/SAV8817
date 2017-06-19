<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 15/06/2017
 * Time: 18:15
 */

namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Offre;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AjaxQueryVenteController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexOffreAction(Request $request)
    {
        $q = $request->get('q');
        $em = $this->getDoctrine()->getManager();
        if (null !== $q) {
            $offres = $em->getRepository(Offre::class)->findBy(['id' => $q]);
        } else {
            $offres = $em->getRepository(Offre::class)->findAll();
        }
        $data = [];
        /** @var Offre $offre */
        foreach ($offres as $offre) {
            $data [] = [
                'id' => $offre->getId(),
                'designation' => $offre->getDesignation(),
                'description' => $offre->getDescription(),
                'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img') . $offre->getImage()
            ];
        }
        return $this->json(['items' => $data]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexBoutiqueAction(Request $request)
    {
        $q = $request->get('q');

        $em = $this->getDoctrine()->getManager();
        $boutiques = $em->getRepository(Offre::class)->findBy(['id' => $q]);
        $data = [];
        /** @var Boutique $boutique */
        foreach ($boutiques as $boutique) {
            $data [] = [
                'id' => $boutique->getId(),
                'designation' => $boutique->getDesignation(),
                'description' => $boutique->getDescription(),
                'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img') . $boutique->getImage()
            ];
        }
        return $this->json(['items' => $data]);
    }

}