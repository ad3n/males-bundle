<?php
/**
 * This file is part of Males Bundle
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Entity;

class EntityTrait
{
    /**
     * @return array
     **/
    public function getProperties()
    {
        return get_object_vars($this);
    }
} 