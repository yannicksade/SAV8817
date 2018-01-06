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
use Vich\UploaderBundle\Event\Event;

class VichEventListener
{
    /**
     * @var CacheManager | null
     */
    private $caheManager; // cache liip
    private $filter;
    /**
     * @var ImageMaker | null
     */
    private $imageMaker;

    function setVars(CacheManager $cacheManager = null, $filter = null, ImageMaker $imageMaker = null)
    {
        $this->caheManager = $cacheManager;
        $this->filter = $filter;
        $this->imageMaker = $imageMaker;
    }

    public function onVichuploaderPreremove(Event $event)
    {
        $object = $event->getObject();
        $mapping = $event->getMapping();
        $image = $mapping->getFileName($object);
        $prefix = $mapping->getUriPrefix();
        $path = $prefix . '/' . $image;
        $this->caheManager->remove($path, $this->filter);
    }

    public function onVichuploaderPostupload(Event $event)
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