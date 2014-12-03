<?php
/**
 * This file is part of Males Bundle
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Entity;

abstract class AbstractEntity implements EntityInterface
{
    public function getFilter()
    {
        return 'name';
    }

    public function getProperties()
    {
        return get_object_vars($this);
    }

    public function __toString()
    {
        return $this->getName();
    }
}