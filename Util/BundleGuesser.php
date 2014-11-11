<?php
/**
 * This file is part of JKN
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Util;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BundleGuesser
{
    /**
     * @var \ReflectionClass
     **/
    protected $reflector;

    /**
     * @var array
     **/
    protected $namespace;

    /**
     * @var string
     **/
    protected $exclude;

    /**
     * @param Controller $controller
     * @return $this
     **/
    public function inizialize(Controller $controller)
    {
        $this->reflector = new \ReflectionClass($controller);
        $this->namespace = explode('\\', $this->reflector->getNamespaceName());
        $key = array_search('__CG__', $this->namespace);//if the controller come from cache

        if ($key) {
            for ($i = $key; $i >= 0; $i--) {
                unset($this->namespace[$i]);
            }
        }

        $this->namespace = array_values($this->namespace);
        $key = array_search('Controller', $this->namespace);
        $lastValue = $this->namespace[count($this->namespace) -1];

        //May be you have nested controller
        if ('Controller' !== $lastValue) {
            $this->exclude = $lastValue;
        }

        for ($i = $key; $i <= count($this->namespace); $i++) {
            array_pop($this->namespace);
        }

        return $this;
    }

    /**
     * @return array|string
     **/
    public function getBundleAlias()
    {
        $namespace = array_unique($this->namespace);
        $key = array_search('Bundle', $namespace);

        if (false !== $key) {
            unset($namespace[$key]);
        }

        $namespace = array_values($namespace);
        $namespace = implode('', $namespace);

        if ('Bundle' === substr($namespace, -6)) {
            return $namespace;
        } else {
            return sprintf('%sBundle', $namespace);
        }
    }

    /**
     * @return string
     **/
    public function getBundleClass()
    {
        return sprintf('%s\%s', $this->getBundleNamespace(), $this->getBundleAlias());
    }

    /**
     * @return string
     **/
    public function getBundleNamespace()
    {
        return implode('\\', $this->namespace);
    }

    /**
     * @return string
     **/
    public function getEntityAlias()
    {
        return sprintf('%s:%s', $this->getBundleAlias(), $this->getIdentity());
    }

    /**
     * @return string
     **/
    public function getEntityClass()
    {
        return sprintf('%s\%s', $this->getEntityNamespace(), $this->getIdentity());
    }

    /**
     * @return string
     **/
    public function getEntityNamespace()
    {
        return sprintf('%s\Entity', $this->getBundleNamespace());
    }

    /**
     * @return string
     **/
    public function getRepositoryNamespace()
    {
        return $this->getEntityNamespace();
    }

    /**
     * @return string
     **/
    public function getRepositoryClass()
    {
        return sprintf('%s\%sRepository', $this->getEntityNamespace(), $this->getIdentity());
    }

    /**
     * @return string
     **/
    public function getFormClass()
    {
        return sprintf('%s\%sType', $this->getFormNamespace(), $this->getIdentity());
    }

    /**
     * @return string
     **/
    public function getFormNamespace()
    {
        return sprintf('%s\Form', $this->getBundleNamespace());
    }

    /**
     * @return string
     **/
    public function getIdentity()
    {
        $shortName = $this->reflector->getShortName();

        if ('Controller' === substr($shortName, -10)) {
            return str_replace($this->exclude, '', substr($shortName, 0, -10));
        } else {
            return $shortName;
        }
    }
}