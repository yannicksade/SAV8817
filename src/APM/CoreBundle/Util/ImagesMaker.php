<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 04/06/2017
 * Time: 22:29
 */

namespace APM\CoreBundle\Util;


use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Storage\StorageInterface;

class ImagesMaker implements ContainerAwareInterface
{
    /**
     * @var Container
     */
    private $container;
    private $filter;

    public function setContainer(ContainerInterface $container = null, $filter = null)
    {
        $this->container = $container;
        $this->filter = $filter;
    }


    public function treatImage($imageIdFile, $object, $suppressOriginalFile = true)
    {
        //-----------------------------Traitement de l'image-----------------------------------------------
        /** @var RequestStack $request */
        $request = $this->container->get('request_stack');
        $data = $request->getCurrentRequest()->request->all();
        $x = $data[$imageIdFile]['x'];
        $y = $data[$imageIdFile]['y'];
        $w = $data[$imageIdFile]['w'];
        $h = $data[$imageIdFile]['h'];

        /** @var StorageInterface $storage */
        $storage = $this->container->get('vich_uploader.storage');
        $path = $storage->resolveUri($object, $imageIdFile);
        $filterManager = $this->container->get('liip_imagine.filter.manager');
        /** @var CacheManager $cacheManager */
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $dataManager = $this->container->get('liip_imagine.data.manager');
        if (!$cacheManager->isStored($path, $this->filter)) { // vérifie si l'image n'existe pas déjà
            $binary = $dataManager->find($this->filter, $path);
            $filteredBinary = $filterManager->applyFilter($binary, $this->filter, array(
                'quality' => 75,
                'filters' => array(
                    'crop' => array(
                        'size' => array($w, $h),
                        'start' => array($x, $y),
                    )
                    /*
                    'thumbnail' => array(
                        'size' => array(600, 514),
                        'mode' => 'outbound',
                    ),
                    'background' => ['size' => [614, 518], 'position' => 'center', 'color' => '#FFF' ],
                    */
                )
            ));
            $cacheManager->store($filteredBinary, $path, $this->filter); //stock l'image
            if ($cacheManager->isStored($path, $this->filter)) { // Test whether the image is really stored
                //$session = $this->container->get('session');
                //$session->getFlashBag()->add('success', 'Image traitée <br/> Résolution:' . '<strong>' . $_w_ . 'x' . $_h_ . '</strong>px.<br> Opération effectuée avec succès');
                if ($suppressOriginalFile) { // suppression de l'image vich d'original
                    /** @var PropertyMappingFactory $propertyMappingFactory */
                    $propertyMappingFactory = $this->container->get('vich_uploader.property_mapping_factory');
                    $storage->remove($object, $propertyMappingFactory->fromField($object, $imageIdFile));
                }
                return true;
            }
        }
        return false;
    }

    public function liipImageResolver($fileName)
    {
        /** @var CacheManager */
        $imagineCacheManager = $this->container->get('liip_imagine.cache.manager');
        /** @var string */
        return $imagineCacheManager->getBrowserPath($this->container->getParameter('images_url') . '/' . $fileName, $this->filter); //$resolvedImage = http//...
    }
}