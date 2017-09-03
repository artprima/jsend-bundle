<?php

/*
 * This file is part of the Artprima Jsend package.
 *
 * (c) Denis Voytyuk <ask@artprima.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Artprima\Bundle\JsendBundle\EventListener;

use Artprima\Bundle\JsendBundle\Controller\Annotations\Jsend;
use Artprima\Bundle\JsendBundle\Serialization\ExceptionExclusionStrategy;
use FOS\RestBundle\Controller\Annotations\View;
use JMS\Serializer\Handler\FormErrorHandler;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\EventListener\TemplateListener;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class JsendResponseListener
 *
 * @author Denis Voytyuk <ask@artprima.cz>
 *
 * @package Artprima\Bundle\EventListener
 */
class JsendResponseListener extends TemplateListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Constructor
     *
     * @param ContainerInterface $container The service container instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        /** @var Jsend $configuration */
        $configuration = $request->attributes->get('_jsend');
        if (!$configuration) {
            return parent::onKernelView($event);
        }

        $view = $event->getControllerResult();

        if ($view instanceof Form && !$view->isValid()) {
            $configuration->setDataVar('form');
            $configuration->setStatus(Jsend::STATUS_FAIL);
        }

        if ($configuration->getStatus() == Jsend::STATUS_FAIL) {
            //fos rest bundle v2.0 and higher
            $viewConfig = $request->attributes->get('_template');
            if (!$viewConfig) {
                //fos rest bundle v 1.7 - 1.8
                $viewConfig = $request->attributes->get('_view');
            }
            if (!$viewConfig) {
                $viewConfig = new View(array());
                $request->attributes->set('_' . $viewConfig->getAliasName(), $viewConfig);
            }
            $viewConfig->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $result = array(
            'status' => $configuration->getStatus(),
            'data' => array(
                $configuration->getDataVar() => $view,
            )
        );

        $view = \FOS\RestBundle\View\View::create();
        if ($version = $request->attributes->get('version')) {
            $context = new Context();
            $context->setVersion($version);
            $view->setContext($context);
        }
        $view->setData($result);

        $event->setControllerResult($view);
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();

        /** @var Jsend $configuration */
        $configuration = $request->attributes->get('_jsend');
        if (!$configuration) {
            return;
        }

        $format = $request->getRequestFormat();
        if (!$format || $format === "html"){
            return;
        }

        $error = $event->getException();

        if (!$error instanceof HttpException) {
            return;
        }

        $result = array(
            'status' => Jsend::STATUS_ERROR,
            'message' => $error->getMessage(),
            'code' => $error->getStatusCode(),
        );

        if ($this->container->getParameter('kernel.environment') == 'dev' && $error->getTrace()) {
            $result['trace'] = $error->getTrace();
        }

        $strategy = new ExceptionExclusionStrategy();
        $context = SerializationContext::create();
        $context->addExclusionStrategy($strategy);

        $content = $this->serializer->serialize($result, $format, $context);
        $response = new Response($content);

        if ($format == 'json') {
            $response->headers->set('Content-Type', 'application/json');
        }
        $event->setResponse($response);
    }
} 
