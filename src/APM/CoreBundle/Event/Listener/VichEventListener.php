<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 03/06/2017
 * Time: 16:04
 */

namespace APM\CoreBundle\Event\Listener;


use APM\CoreBundle\Util\ImagesMaker;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Storage\StorageInterface;
use FOS\RestBundle\Request\ParamFetcher;
class VichEventListener
{
    /**
     * @var CacheManager | null
     */
    private $mediaCaheManager; // cache liip
    private $filter;
    /**
     * @var StorageInterface
     */
    private $storage;
    /**
     * @var ImagesMaker
     */
    private $imagesMaker;

    function setVars(ImagesMaker $imageMaker = null, StorageInterface $storage = null, CacheManager $mediaCaheManager = null, $filter = null)
    {
        $this->storage = $storage;
        $this->mediaCaheManager = $mediaCaheManager;
        $this->filter = $filter;
        $this->imagesMaker = $imageMaker;

    }

    /**
     * supprimer les filtres
     * Remove cached filters
     * @param Event $event
     */
    public function onVichuploaderPreremove(Event $event)
    {
        $object = $event->getObject();
        $mapping = $event->getMapping();
        $image = $mapping->getUploadDir($object) . '/' . $mapping->getFileName($object);
        $prefix = $mapping->getUriPrefix();
        $path = $prefix . '/' . $image;
        $this->mediaCaheManager->remove($path, $this->filter);
    }
    public function onVichuploaderPreupload(Event $event)
    {// remove vich files
    }

    public function onVichuploaderPreinject(Event $event)
    {
        // $object = $event->getObject();

        //$this->imagesMaker->removeFile('imageFile', $object);
    }

    public function onVichuploaderPostinject(Event $event)
    {
        // $object = $event->getObject();

        //$this->imagesMaker->removeFile('imageFile', $object);
    }

    public function onVichuploaderPostupload(Event $event)
    {
        $object = $event->getObject();
        $imageIdFile = $event->getMapping()->getFilePropertyName();

        $this->imagesMaker->treatImage($imageIdFile, $object);

    }

}