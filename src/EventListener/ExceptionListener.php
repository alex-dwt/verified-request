<?php

/*
 * This file is part of the VerifiedRequestBundle package.
 * (c) Alexander Lukashevich <aleksandr.dwt@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace AlexDwt\VerifiedRequestBundle\EventListener;

use AlexDwt\VerifiedRequestBundle\Exception\IncorrectInputParamsException;
use AlexDwt\VerifiedRequestBundle\Response\IncorrectInputParamsResponseInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    /**
     * @var IncorrectInputParamsResponseInterface
     */
    private $incorrectInputParamsResponse;

    public function __construct(IncorrectInputParamsResponseInterface $incorrectInputParamsResponse)
    {
        $this->incorrectInputParamsResponse = $incorrectInputParamsResponse;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof IncorrectInputParamsException) {
            return;
        }

        $event->setResponse(
            $this
                ->incorrectInputParamsResponse
                ->getResponse($exception->getErrors())
        );
    }
}
