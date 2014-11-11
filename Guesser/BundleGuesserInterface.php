<?php
/**
 * This file is part of Males Bundle
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Guesser;

interface BundleGuesserInterface
{
    public function getBundleAlias();

    public function getBundleClass();

    public function getBundleNamespace();

    public function getEntityAlias();

    public function getEntityClass();

    public function getEntityNamespace();

    public function getRepositoryNamespace();

    public function getRepositoryClass();

    public function getFormClass();

    public function getFormNamespace();

    public function getIdentity();
} 