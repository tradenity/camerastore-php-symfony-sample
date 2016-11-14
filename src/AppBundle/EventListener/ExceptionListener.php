<?php
/**
 * Created by IntelliJ IDEA.
 * User: Joseph
 * Date: 14-Nov-16
 * Time: 11:49 AM
 */

namespace AppBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Tradenity\SDK\Exceptions\EntityNotFoundException;
use Tradenity\SDK\Exceptions\SessionExpiredException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tradenity\SDK\TradenityClient;

class ExceptionListener
{
    private $container;

    /**
     * @param mixed $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getException();


        if ($exception instanceof SessionExpiredException) {
            TradenityClient::resetCurrentSession();
            $response = new RedirectResponse('/');
            $event->setResponse($response);
        } else if ($exception instanceof EntityNotFoundException) {
            $response = new Response();
            $templating = $this->container->get('templating');
            $response->setContent($templating->render('error/not_found.html.twig', []));
            $event->setResponse($response);
        }



    }
}