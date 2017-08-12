<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 04/06/2017
 * Time: 22:29
 */

namespace APM\CoreBundle\Util;


use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Storage\FileSystemStorage;

class CropMaker implements ContainerAwareInterface
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


    /**
     * @param $_x_
     * @param $_y_
     * @param $_w_
     * @param $_h_
     * @param $fileName
     * @param Object_ $object
     */
    public function setCropParameters($_x_, $_y_, $_w_, $_h_, $fileName, $object = null)
    {
        //-----------------------------Traitement de l'image-----------------------------------------------
        $path = $this->container->getParameter('images_url') . '/' . $fileName;
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
                        'size' => array($_w_, $_h_),
                        'start' => array($_x_, $_y_),
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
                $session = $this->container->get('session');
                $session->getFlashBag()->add('success', 'Image traitée <br/> Résolution:' . '<strong>' . $_w_ . 'x' . $_h_ . '</strong>px.<br> Opération effectuée avec succès');
                if ($object) { // suppression de l'image vich d'origine
                    /** @var FileSystemStorage $storage */
                    $storage = $this->container->get('vich_uploader.storage');
                    /** @var PropertyMappingFactory $propertyMappingFactory */
                    $propertyMappingFactory = $this->container->get('vich_uploader.property_mapping_factory');
                    $storage->remove($object, $propertyMappingFactory->fromField($object, 'imageFile'));
                }
            }
        }

    }

    public function liipImageResolver($fileName)
    {
        /** @var CacheManager */
        $imagineCacheManager = $this->container->get('liip_imagine.cache.manager');
        /** @var string */
        return $imagineCacheManager->getBrowserPath($this->container->getParameter('images_url') . '/' . $fileName, $this->filter); //$resolvedImage = http//...
    }
}