<?php

/*
 * This file is part of the VerifiedRequestBundle package.
 * (c) Alexander Lukashevich <aleksandr.dwt@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace AlexDwt\VerifiedRequestBundle\Request;

use AlexDwt\VerifiedRequestBundle\Exception\IncorrectInputParamsException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;

abstract class VerifiedRequest
{
    /**
     * @var array
     */
    private $inputParams;

    /**
     * @var RecursiveValidator
     */
    private $validator;

    public function __construct(ValidatorInterface $validator, RequestStack $requestStack)
    {
        $this->validator = $validator;

        if ($request = $requestStack->getCurrentRequest()) {
            $this->populateFromRequest($request);
        }
    }

    public function __call($name, $arguments)
    {
        $inputParams = $this->getInputParams();

        if (substr($name, 0, 3) === 'get'
            && strlen($name) >= 4
        ) {
            $paramName = lcfirst(substr($name, 3));
            if (array_key_exists($paramName, $inputParams)) {
                return $inputParams[$paramName];
            } else {
                return $this->getOptionalFields()[$paramName] ?? null;
            }
        }

        throw new \RuntimeException('Unsupported method to call');
    }

    public function getInputParams(): array
    {
        if ($this->inputParams === null) {
            throw new \RuntimeException('InputParams is not populated');
        }

        return $this->inputParams;
    }

    public function populateFromArray(array $inputParams, bool $runValidation = true): self
    {
        if ($runValidation) {
            $this->validate($inputParams);
        }

        $this->inputParams = $inputParams;

        return $this;
    }

    abstract protected function getValidationRules(): array;

    protected function getOptionalFields(): array
    {
        return [];
    }

    private function populateFromRequest(Request $request)
    {
        $inputParams = array_merge(
            $request->query->all(),
            $request->request->all(),
            (array) json_decode(file_get_contents('php://input'), true)
        );

        $this->validate($inputParams);

        $this->inputParams = $inputParams;
    }

    private function validate(array &$params)
    {
        /** @var array $fields */
        $fields = $this->getValidationRules();

        if (!$fields) {
            $params = [];

            return;
        }

        // apply preFilter only to first level of array rules if needed
        foreach ($fields as $fieldName => $rules) {
            if (is_array($rules) && isset($rules[0])) {
                if (($preCallback = $rules[0]) instanceof \Closure) {
                    if (array_key_exists($fieldName, $params)) {
                        $preCallback($params[$fieldName]);
                    }
                    $fields[$fieldName] = array_slice($rules, 1);
                }
            }
        }

        // mark optional fields
        if ($optionalFields = array_keys($this->getOptionalFields())) {
            foreach ($fields as $fieldName => $rules) {
                if (in_array($fieldName, $optionalFields)) {
                    $fields[$fieldName] = new Assert\Optional($rules);
                }
            }
        }

        $constraints = new Assert\Collection([
            'fields' => $fields,
//            'allowMissingFields' => true,
//            'missingFieldsMessage' => true,
//            'allowExtraFields' => false,
//            'extraFieldsMessage' => true,
        ]);

        if (count(
            $violations = $this->validator->validate(
                $params,
                $constraints
            )
        )) {
            $message = '';
            foreach ($violations as $violation) {
                $message .= sprintf(
                    "%s: %s\r\n",
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
            }
            throw new IncorrectInputParamsException($message);
        }
    }
}
