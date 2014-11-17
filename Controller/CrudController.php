<?php
/**
 * This file is part of Males Bundle
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Ihsan\MalesBundle\IhsanMalesBundle as Constant;
use Ihsan\MalesBundle\Form\AbstractType;
use Ihsan\MalesBundle\Entity\EntityInterface;

abstract class CrudController extends Controller
{
    /**
     * @var AbstractType
     **/
    protected $formType;

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
     * @param EntityInterface $entity
     * @param null $entityClassName
     */
    public function __construct(ContainerInterface $container, AbstractType $formType, EntityInterface $entity, $entityClassName = null)
    {
        $this->container = $container;
        $this->formType = $formType;
        $this->guesser = $this->container->get('males.guesser');
        $this->guesser->initialize($this, $entityClassName);
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

            if (! $form->isValid()) {
                return $this->render(sprintf('%s:%s:new.html.twig', $this->guesser->getBundleAlias(), $this->guesser->getIdentity()), array(
                    'form' => $form->createView(),
                ));
            }

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
     * @param bool $upperCaseFilter
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($upperCaseFilter = true)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($this->guesser->getEntityAlias());
        $request = $this->container->get('request');

        /**
         * @var QueryBuilder
         **/
        $qb = $repo->createQueryBuilder('o')->select('o')->addOrderBy('o.id', 'DESC');
        $filter = $upperCaseFilter ? strtoupper($request->query->get('filter')) : $request->query->get('filter');

        if ($filter) {
            $qb->andWhere(sprintf('o.%s LIKE :filter', $this->entity->getFilter()))
                ->setParameter('filter', strtr('%filter%', array('filter' => $filter)));
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
                'filter' => $filter,
            )
        );
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     **/
    public function showAction($id)
    {
        $responseFormat = $this->container->getParameter('males.response.type');
        $data = $this->existOrNotFoundException($id);

        if ('html' !== $responseFormat) {
            new Response($this->serializer->serialize($data, array(), $responseFormat));
        }

        return $this->render(sprintf('%s:%s:show.html.twig', $this->guesser->getBundleAlias(), $this->guesser->getIdentity()), array(
            'data' => $data,
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

            if ($form->isValid()) {
                $em->persist($form->getData());
                $em->flush();
            }

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

    /**
     * @param $id
     * @throws NotFoundHttpException
     * @return object
     **/
    protected function existOrNotFoundException($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($this->guesser->getEntityAlias())->find($id);

        if (! $entity) {
            throw new NotFoundHttpException($this->get('translator')->trans('message.not_found', array('data' => $id), $this->container->getParameter('bundle')));
        }

        return $entity;
    }

    public function hasJoinProperty($property)
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getClassMetadata($this->guesser->getEntityAlias())->getAssociationMapping($property);
    }
}