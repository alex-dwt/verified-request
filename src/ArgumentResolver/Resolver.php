<?php

/*
 * This file is part of the VerifiedRequestBundle package.
 * (c) Alexander Lukashevich <aleksandr.dwt@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace AlexDwt\VerifiedRequestBundle\ArgumentResolver;

use AlexDwt\VerifiedRequestBundle\Request\VerifiedRequest;
use AlexDwt\VerifiedRequestBundle\Request\VerifiedRequestCreator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class Resolver implements ArgumentValueResolverInterface
{
    /**
     * @var VerifiedRequestCreator
     */
    private $requestCreator;

    public function __construct(VerifiedRequestCreator $requestCreator)
    {
        $this->requestCreator = $requestCreator;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return (new \ReflectionClass($argument->getType()))
            ->isSubclassOf(VerifiedRequest::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->requestCreator->createFromRequest($argument->getType(), $request);
    }
}
