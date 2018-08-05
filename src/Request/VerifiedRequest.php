<?php

/*
 * This file is part of the VerifiedRequestBundle package.
 * (c) Alexander Lukashevich <aleksandr.dwt@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace AlexDwt\VerifiedRequestBundle\Request;

use AlexDwt\VerifiedRequestBundle\Exception\IncorrectInputParamsException;
use AlexDwt\VerifiedRequestBundle\Exception\InputParamNotFoundException;

abstract class VerifiedRequest
{
    /**
     * @var array
     */
    private $inputParams;

    public function __construct(array $inputParams)
    {
        $this->inputParams = $inputParams;
    }

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) === 'get'
            && strlen($name) >= 4
        ) {
            $paramName = lcfirst(substr($name, 3));
            if (array_key_exists($paramName, $this->inputParams)) {
                return $this->inputParams[$paramName];
            } else {
                throw new InputParamNotFoundException(
                    "Requested input param '$paramName' is not found"
                );
            }
        }

        throw new \RuntimeException('Unsupported method to call');
    }

    abstract public static function getValidationRules(): array;
}
