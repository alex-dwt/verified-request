<?php

/*
 * This file is part of the VerifiedRequestBundle package.
 * (c) Alexander Lukashevich <aleksandr.dwt@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace AlexDwt\VerifiedRequestBundle\Response;

use Symfony\Component\HttpFoundation\Response;

interface IncorrectInputParamsResponseInterface
{
    public function getResponse(array $errors): Response;
}
