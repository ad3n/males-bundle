<?php
/**
 * This file is part of JKN
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Form;

use Ihsan\MalesBundle\Util\BundleGuesser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\AbstractType as BaseType;

abstract class AbstractType extends BaseType
{
    protected $guesser;

    public function __construct(BundleGuesser $guesser)
    {
        $this->guesser = $guesser;
    }

    public function setController(Controller $controller)
    {
        $this->guesser->inizialize($controller);
    }
}