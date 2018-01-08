<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 03/06/2017
 * Time: 16:04
 */

namespace APM\CoreBundle\Event\Listener;


use APM\CoreBundle\Util\ImageMaker;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Handler\UploadHandler;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Storage\FileSystemStorage;
use Vich\UploaderBundle\Storage\StorageInterface;

class VichEventListener
{
    /**
     * @var CacheManager | null
     */
    private $mediaCaheManager; // cache liip
    private $filter;
    /**
     * @var UploadHandler
     */
    private $handler;

    function setVars(UploadHandler $handler = null, CacheManager $mediaCaheManager = null, $filter = null)
    {
        $this->handler = $handler;
        $this->mediaCaheManager = $mediaCaheManager;

        $this->filter = $filter;

    }

    /**
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
        $object = $event->getObject();
        $imageIdFile = $event->getMapping()->getFilePropertyName();
        $this->handler->remove($object, $imageIdFile);
    }

    public function onVichuploaderPostupload(Event $event)
    {
        // $object = $event->getObject();
        //$this->imageMaker->removeFile('imageFile', $object);
    }

    public function onVichuploaderPreinject(Event $event)
    {
        // $object = $event->getObject();

        //$this->imageMaker->removeFile('imageFile', $object);
    }

    public function onVichuploaderPostinject(Event $event)
    {
        // $object = $event->getObject();

        //$this->imageMaker->removeFile('imageFile', $object);
    }


}