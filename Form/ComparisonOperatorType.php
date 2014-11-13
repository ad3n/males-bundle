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

class ComparisonOperatorType extends AbstractType
{
    const FORM_NAME = 'compare';

    protected $operators;

    public function __construct(array $operators = array())
    {
        $this->operators = $operators;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        if (empty($this->operators)) {
            $this->operators = array(
                '=' => '=',
                '<>' => '<>',
                '<' => '<',
                '>' => '>',
                '<=' => '<=',
                '>=' => '>=',
                'LIKE' => 'LIKE',
                'BETWEEN' => 'BETWEEN',
                'IN' => 'IN',
            );
        }

        $resolver->setDefaults(array('choices' => $this->operators));
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