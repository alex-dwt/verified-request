<?php

/*
 * This file is part of the VerifiedRequestBundle package.
 * (c) Alexander Lukashevich <aleksandr.dwt@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace AlexDwt\VerifiedRequestBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
//        // You get the exception object from the received event
//        $exception = $event->getException();
//        $message = sprintf(
//            'My Error says: %s with code: %s',
//            $exception->getMessage(),
//            $exception->getCode()
//        );
//
//        return;
//
//        // Customize your response object to display the exception details
//        $response = new Response();
//        $response->setContent($message);
//
//        // HttpExceptionInterface is a special type of exception that
//        // holds status code and header details
//        if ($exception instanceof HttpExceptionInterface) {
//            $response->setStatusCode($exception->getStatusCode());
//            $response->headers->replace($exception->getHeaders());
//        } else {
//            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
//        }
//
//        // sends the modified response object to the event
//        $event->setResponse($response);
    }
}
