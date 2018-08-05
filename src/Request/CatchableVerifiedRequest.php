<?php

/*
 * This file is part of the VerifiedRequestBundle package.
 * (c) Alexander Lukashevich <aleksandr.dwt@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace AlexDwt\VerifiedRequestBundle\Request;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class CatchableVerifiedRequest extends VerifiedRequest
{
    public function getResponse(string $errorMessage): Response
    {
        return new JsonResponse(
            ['errorMessage' => $errorMessage],
            422
        );
    }
}
