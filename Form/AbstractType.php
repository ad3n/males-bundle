<?php
/**
 * This file is part of Males Bundle
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Form;

use Ihsan\MalesBundle\Guesser\BundleGuesserInterface;
use Symfony\Component\Form\AbstractType as BaseType;

abstract class AbstractType extends BaseType
{
    /**
     * @var BundleGuesserInterface
     **/
    protected $guesser;

    /**
     * @param BundleGuesserInterface $guesser
     **/
    public function __construct(BundleGuesserInterface $guesser)
    {
        $this->guesser = $guesser;
    }
}