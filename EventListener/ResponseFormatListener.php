<?php
/**
 * This file is part of Males Bundle
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
namespace Ihsan\MalesBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Ihsan\MalesBundle\IhsanMalesBundle as Constant;
use Ihsan\MalesBundle\Serializer\Serializer;

class ResponseFormatListener
{
    protected $responseFormat;

    protected $session;

    protected $serializer;

    public function __construct(ContainerInterface $container, Session $session, Serializer $serializer)
    {
        $this->responseFormat = $container->getParameter('males.response.type');
        $this->session = $session;
        $this->serializer = $serializer;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ('html' === $this->responseFormat) {
            return;
        }

        $flashBag = $this->session->getFlashBag();
        $message = $flashBag->get(Constant::MESSAGE_SAVE, $flashBag->get(Constant::MESSAGE_UPDATE, $flashBag->get(Constant::MESSAGE_DELETE)));
        $event->getResponse()->setContent($this->serializer->serialize($message, array(), $this->responseFormat));
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ('html' === $this->responseFormat) {
            return;
        }

        $exception = $event->getException();
        $response = $event->getResponse();
        $response->setContent($this->serializer->serialize($exception->getMessage(), array(), $this->responseFormat));
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 