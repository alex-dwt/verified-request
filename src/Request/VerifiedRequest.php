<?php

/*
 * This file is part of the VerifiedRequestBundle package.
 * (c) Alexander Lukashevich <aleksandr.dwt@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace AlexDwt\VerifiedRequestBundle\Request;

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
                return static::getOptionalFields()[$paramName] ?? null;
            }
        }

        throw new \RuntimeException('Unsupported method to call');
    }

    public function getInputArray(): array
    {
        return $this->inputParams;
    }

    abstract public static function getValidationRules(): array;

    public static function getOptionalFields(): array
    {
        return [];
    }
}
