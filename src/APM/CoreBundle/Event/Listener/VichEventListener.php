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
    private $caheManager; // cache liip
    private $filter;
    /**
     * @var UploadHandler
     */
    private $handler;

    function setVars(UploadHandler $handler = null, CacheManager $cacheManager = null, $filter = null)
    {
        $this->handler = $handler;
        $this->caheManager = $cacheManager;

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
        $image = $mapping->getFileName($object);
        $prefix = $mapping->getUriPrefix();
        $path = $prefix . '/' . $image;
        $this->caheManager->remove($path, $this->filter);
    }

    public function onVichuploaderPreupload(Event $event)
    {// remove vich files
        $object = $event->getObject();
        $imageIdFile = $event->getMapping()->getFileNamePropertyName();
        $this->handler->remove($object, $imageIdFile);


        /*for ($i=1; $i<=10; $i++){
            if($imageIdFile === 'image'.$i .'File'){
                $getImageId = 'getImage'.$i;
               $fileName = $object->$getImageId();

            }

        }*/

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