<?php

/*
 * This file is part of the VerifiedRequestBundle package.
 * (c) Alexander Lukashevich <aleksandr.dwt@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace AlexDwt\VerifiedRequestBundle\ArgumentResolver;

use AlexDwt\VerifiedRequestBundle\Request\VerifiedRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class Resolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return (new \ReflectionClass($argument->getType()))
            ->isSubclassOf(VerifiedRequest::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $requestClassName = $argument->getType();

        /** @var VerifiedRequest $result */
        $result = new $requestClassName();

        $result->setInputParams([]);

        yield $result;
    }
}
