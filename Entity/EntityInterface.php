<?php
/**
 * This file is part of Males Bundle
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Entity;

interface EntityInterface
{
    /**
     * @return string
     **/
    public function getName();

    /**
     * @return string
     **/
    public function getFilter();

    /**
     * @return array
     **/
    public function getProperties();
}