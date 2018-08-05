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

    public function setInputParams(array $params, bool $runValidation = true): self
    {
        $this->inputParams = $params;

        if ($runValidation) {
            $this->validate();
        }

        return $this;
    }

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) === 'get'
            && strlen($name) >= 4
        ) {
            $paramName = lcfirst(substr($name, 3));
            if (in_array($paramName, $this->inputParams, true)) {
                return $this->inputParams[$paramName];
            } else {
                throw new InputParamNotFoundException(
                    "Requested input param '$paramName' is not found"
                );
            }
        }

        throw new \RuntimeException('Unsupported method to call');
    }

    abstract protected function getValidationRules(): array;

    private function validate()
    {
        $isValid = true;

        $message = 'validation errors';

        if (!$isValid) {
            throw new IncorrectInputParamsException($message);
        }
    }
}
