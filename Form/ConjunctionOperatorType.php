<?php
/**
 * This file is part of Males Bundle
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConjunctionOperatorType extends AbstractType
{
    const FORM_NAME = 'conjunction';

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('choices' => array('AND' => 'AND', 'OR' => 'OR')));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return self::FORM_NAME;
    }
} 