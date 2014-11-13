<?php
/**
 * This file is part of Males Bundle
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Serializer;

use JMS\Serializer\Serializer as JMSSerializer;
use JMS\Serializer\SerializationContext;

class Serializer
{
    /**
     * @var JMSSerializer
     **/
    protected $serializer;

    /**
     * @param JMSSerializer $serializer
     **/
    public function __construct(JMSSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param $data
     * @param $groups
     * @param $format
     * @return mixed
     **/
    public function serialize($data, $groups, $format)
    {
        return $this->serializer->serialize($data, $format, SerializationContext::create()->setGroups(array_merge(array('Default'), $groups)));
    }

    /**
     * @param $data
     * @param $type
     * @param $format
     * @return mixed
     **/
    public function deserialize($data, $type, $format)
    {
        return $this->serializer->deserialize($data, $type, $format);
    }
}