<?php
/**
 * This file is part of JKN
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Ihsan\MalesBundle\IhsanMalesBundle as Constant;
use Ihsan\MalesBundle\Form\AbstractType;
use Ihsan\MalesBundle\Entity\EntityInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CrudController extends Controller
{
    protected $formType;

    protected $entity;

    protected $guesser;

    public function __construct(ContainerInterface $container, AbstractType $formType, EntityInterface $entity)
    {
        $this->container = $container;
        $this->formType = $formType;
        $this->formType->setController($this);
        $this->guesser = $this->container->get('males.guesser');
        $this->entity = $entity;
    }

    public function newAction(Request $request)
    {
        $form = $this->createForm($this->formType, $this->entity);

        if ($request->isMethod('post')) {
            $em = $this->getDoctrine()->getManager();
            $form->handleRequest($request);

            $em->persist($form->getData());
            $em->flush();

            $session = $this->container->get('session');
            $session->getFlashBag()->set('message.save', $this->get('translator')->trans('message.save', array('data' => $form->getData()->getName()), $this->container->getParameter('bundle')));

            return $this->redirect($this->generateUrl(sprintf('%s_index', strtolower($this->guesser->getIdentity()))));
        }

        return $this->render(sprintf('%s:%s:new.html.twig', $this->guesser->getBundleAlias(), $this->guesser->getIdentity()), array(
            'form' => $form->createView(),
        ));
    }

    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($this->guesser->getEntityAlias());

        $qb = $repo->createQueryBuilder('o')->select('o')->addOrderBy('o.id', 'DESC');
        $page = $request->query->get('page', 1);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $page,
            Constant::RECORD_PER_PAGE
        );

        return $this->render(sprintf('%s:%s:list.html.twig', $this->guesser->getBundleAlias(), $this->guesser->getIdentity()),
            array('data' => $pagination, 'start' => ($page - 1) * Constant::RECORD_PER_PAGE)
        );
    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($this->guesser->getEntityAlias())->find($id);

        if (! $entity) {
            throw new NotFoundHttpException($this->get('translator')->trans('message.not_found', array('data' => $id), $this->container->getParameter('bundle')));
        }

        return $this->render(sprintf('%s:%s:show.html.twig', $this->guesser->getBundleAlias(), $this->guesser->getIdentity()), array(
            'data' => $entity,
        ));
    }

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($this->guesser->getEntityAlias())->find($id);

        if (! $entity) {
            throw new NotFoundHttpException($this->get('translator')->trans('message.not_found', array('data' => $id), $this->container->getParameter('bundle')));
        }

        $form = $this->createForm($this->formType, $entity);
        $request = $this->container->get('request');

        if ($request->isMethod('post')) {
            $em = $this->getDoctrine()->getManager();
            $form->handleRequest($request);

            $em->persist($form->getData());
            $em->flush();

            $session = $this->container->get('session');
            $session->getFlashBag()->set('message.update', $this->get('translator')->trans('message.update', array('data' => $form->getData()->getName()), $this->container->getParameter('bundle')));

            return $this->redirect($this->generateUrl(sprintf('%s_index', strtolower($this->guesser->getIdentity()))));
        }

        return $this->render(sprintf('%s:%s:edit.html.twig', $this->guesser->getBundleAlias(), $this->guesser->getIdentity()), array(
            'form' => $form->createView(),
            'id' => $id,
        ));
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($this->guesser->getEntityAlias())->find($id);

        if (! $entity) {
            throw new NotFoundHttpException($this->get('translator')->trans('message.not_found', array('data' => $id), $this->container->getParameter('bundle')));
        }

        $request = $this->container->get('request');

        if ($request->isMethod('post')) {
            $em->remove($entity);
            $em->flush();

            $session = $this->container->get('session');
            $session->getFlashBag()->set('message.delete', $this->get('translator')->trans('message.delete', array('data' => $entity->getName()), $this->container->getParameter('bundle')));

            return $this->redirect($this->generateUrl(sprintf('%s_index', strtolower($this->guesser->getIdentity()))));
        }

        return $this->render(sprintf('%s:%s:delete.html.twig', $this->guesser->getBundleAlias(), $this->guesser->getIdentity()), array(
            'data' => $entity,
        ));
    }
}