<?php
/**
 * This file is part of Males Bundle
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Ihsan\MalesBundle\IhsanMalesBundle as Constant;
use Ihsan\MalesBundle\Form\AbstractType;
use Ihsan\MalesBundle\Entity\EntityInterface;
use Ihsan\MalesBundle\Form\AbstractFilter;

abstract class CrudController extends Controller
{
    /**
     * @var AbstractType
     **/
    protected $formType;

    /**
     * @var AbstractFilter
     **/
    protected $formFilter;

    /**
     * @var EntityInterface
     **/
    protected $entity;

    /**
     * @var \Ihsan\MalesBundle\Guesser\BundleGuesser
     **/
    protected $guesser;

    /**
     * @param ContainerInterface $container
     * @param AbstractType $formType
     * @param AbstractFilter $formFilter
     * @param EntityInterface $entity
     **/
    public function __construct(ContainerInterface $container, AbstractType $formType, AbstractFilter $formFilter, EntityInterface $entity)
    {
        $this->container = $container;
        $this->formType = $formType;
        $this->formFilter = $formFilter;
        $this->guesser = $this->container->get('males.guesser');
        $this->guesser->initialize($this);
        $this->entity = $entity;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     **/
    public function newAction(Request $request)
    {
        $form = $this->createForm($this->formType, $this->entity);

        if ($request->isMethod('post')) {
            $em = $this->getDoctrine()->getManager();
            $form->handleRequest($request);

            $em->persist($form->getData());
            $em->flush();

            $session = $this->container->get('session');
            $session->getFlashBag()->set(Constant::MESSAGE_SAVE, $this->get('translator')->trans(Constant::MESSAGE_SAVE, array('data' => $form->getData()->getName()), $this->container->getParameter('bundle')));

            return $this->redirect($this->generateUrl(sprintf('%s_index', strtolower($this->guesser->getIdentity()))));
        }

        return $this->render(sprintf('%s:%s:new.html.twig', $this->guesser->getBundleAlias(), $this->guesser->getIdentity()), array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     **/
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($this->guesser->getEntityAlias());
        $qb = $repo->createQueryBuilder('o')->select('o')->addOrderBy('o.id', 'DESC');
        $mode = $request->query->get('mode');
        $filter = strtoupper($request->query->get('filter'));

        if ($filter && 'basic' === $mode) {
            $qb->andWhere(sprintf('o.%s LIKE :filter', $this->entity->getFilter()))
                ->setParameter('filter', strtr('%filter%', array('filter' => $filter)));
        }

        if ('advance' === $mode) {

        }

        $page = $request->query->get('page', 1);
        $paginator  = $this->container->get('knp_paginator');

        $pagination = $paginator->paginate(
            $qb,
            $page,
            Constant::RECORD_PER_PAGE
        );

        return $this->render(sprintf('%s:%s:list.html.twig', $this->guesser->getBundleAlias(), $this->guesser->getIdentity()),
            array(
                'data' => $pagination,
                'start' => ($page - 1) * Constant::RECORD_PER_PAGE,
                'mode' => $mode,
                'filter' => $filter
            )
        );
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     **/
    public function showAction($id)
    {
        return $this->render(sprintf('%s:%s:show.html.twig', $this->guesser->getBundleAlias(), $this->guesser->getIdentity()), array(
            'data' => $this->existOrNotFoundException($id),
        ));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     **/
    public function editAction($id)
    {
        $entity = $this->existOrNotFoundException($id);
        $form = $this->createForm($this->formType, $entity);
        $request = $this->container->get('request');

        if ($request->isMethod('post')) {
            $em = $this->getDoctrine()->getManager();
            $form->handleRequest($request);

            $em->persist($form->getData());
            $em->flush();

            $session = $this->container->get('session');
            $session->getFlashBag()->set(Constant::MESSAGE_UPDATE, $this->get('translator')->trans(Constant::MESSAGE_UPDATE, array('data' => $form->getData()->getName()), $this->container->getParameter('bundle')));

            return $this->redirect($this->generateUrl(sprintf('%s_index', strtolower($this->guesser->getIdentity()))));
        }

        return $this->render(sprintf('%s:%s:edit.html.twig', $this->guesser->getBundleAlias(), $this->guesser->getIdentity()), array(
            'form' => $form->createView(),
            'id' => $id,
        ));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     **/
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->existOrNotFoundException($id);
        $request = $this->container->get('request');

        if ($request->isMethod('post')) {
            $em->remove($entity);
            $em->flush();

            $session = $this->container->get('session');
            $session->getFlashBag()->set(Constant::MESSAGE_DELETE, $this->get('translator')->trans(Constant::MESSAGE_DELETE, array('data' => $entity->getName()), $this->container->getParameter('bundle')));

            return $this->redirect($this->generateUrl(sprintf('%s_index', strtolower($this->guesser->getIdentity()))));
        }

        return $this->render(sprintf('%s:%s:delete.html.twig', $this->guesser->getBundleAlias(), $this->guesser->getIdentity()), array(
            'data' => $entity,
        ));
    }

    protected function existOrNotFoundException($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($this->guesser->getEntityAlias())->find($id);

        if (! $entity) {
            throw new NotFoundHttpException($this->get('translator')->trans('message.not_found', array('data' => $id), $this->container->getParameter('bundle')));
        }

        return $entity;
    }

    public function filterAction()
    {
        return $this->render(sprintf('%s:%s:filter.html.twig', $this->guesser->getBundleAlias(), $this->guesser->getIdentity()), array(
            'form' => $this->createForm($this->formFilter),
        ));
    }
}