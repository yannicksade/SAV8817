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
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\File;

class VichEventListener
{
    /**
     * @var CacheManager | null
     */
    private $mediaCaheManager; // cache liip
    /**
     * @var StorageInterface
     */
    private $storage;
    /**
     * @var ImagesMaker
     */
    private $imagesMaker;

    function setVars(ImagesMaker $imageMaker = null, CacheManager $mediaCaheManager = null, StorageInterface $storage = null)
    {
        $this->storage = $storage;
        $this->mediaCaheManager = $mediaCaheManager;
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
        $this->mediaCaheManager->remove($path);
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
        $mapping = $event->getMapping();
        $imageIdFile = $mapping->getFilePropertyName();
        $path = $mapping->getUriPrefix() . '/' . $mapping->getUploadDir($object) . '/' . $mapping->getFileName($object);
        $file = new File\File($path);
        $fileMimeType = $file->getMimeType();
        if ($fileMimeType === "image/jpeg" || $fileMimeType === "image/png" || $fileMimeType === "image/gif") {
            $this->imagesMaker->treatImage($imageIdFile, $object);
        }
    }

}