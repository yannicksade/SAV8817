<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 11/12/2017
 * Time: 20:42
 */

namespace APM\CoreBundle\Trade;

use JMS\Serializer\Serializer;
use JMS\SerializerBundle\ContextFactory\ConfiguredContextFactory;

class DataSerializedHandler
{
    private $contextFactory;
    private $serializer;

    public function __construct(Serializer $serializer, ConfiguredContextFactory $contextFactory)
    {
        $this->serializer = $serializer;
        $this->contextFactory = $contextFactory;
    }

    public function getFormalData($data, array $group, $activeMaxDepth = false)
    {

        /** @var ConfiguredContextFactory $serializerContext */
        $serializerContext = $this->contextFactory->createSerializationContext();
        if ($activeMaxDepth) {
            $serializerContext->enableMaxDepthChecks();
        }
        $serializerContext->setGroups($group);
        return $this->serializer->serialize($data, 'json', $serializerContext);
    }
}