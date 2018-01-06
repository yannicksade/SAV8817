<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 05/01/2018
 * Time: 21:35
 */

namespace APM\CoreBundle\Util;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class DirectoryNamer implements DirectoryNamerInterface
{
    public function directoryName($object, PropertyMapping $mapping)
    {
        return $mapping->getMappingName(); //. $object->getDesignation();
    }
}