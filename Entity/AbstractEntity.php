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
}