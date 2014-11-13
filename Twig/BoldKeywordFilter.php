<?php
/**
 * This file is part of Males Bundle
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Twig;


class BoldKeywordFilter extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('bold_keyword', array($this, 'boldKeyword')),
        );
    }

    public function boldKeyword($string, $keyword, $mode = 'basic')
    {
        if ('basic' !== $mode) {
            return $string;
        }

        return str_replace($keyword, sprintf('<strong>%s</strong>', $keyword), $string);
    }

    public function getName()
    {
        return 'bold_keyword_filter';
    }
} 