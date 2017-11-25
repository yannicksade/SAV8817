<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 03/06/2017
 * Time: 16:04
 */

namespace APM\CoreBundle\Event\Listener;


use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Event\Event;

class VichEventListener
{
    /**
     * @var CacheManager
     */
    private $caheManager;
    private $filter;

    function __construct(CacheManager $cacheManager, $filter)
    {
        $this->caheManager = $cacheManager;
        $this->filter = $filter;
    }

    public function onVichuploaderPreremove(Event $event)
    {
        $object = $event->getObject();
        $prefix = $event->getMapping()->getUriPrefix();
        $path = $prefix . '/' . $object->getImage();
        $this->caheManager->remove($path, $this->filter);
    }

}