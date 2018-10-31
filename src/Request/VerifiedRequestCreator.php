<?php

/*
 * This file is part of the VerifiedRequestBundle package.
 * (c) Alexander Lukashevich <aleksandr.dwt@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace AlexDwt\VerifiedRequestBundle\Request;

use AlexDwt\VerifiedRequestBundle\Exception\IncorrectInputParamsException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class VerifiedRequestCreator
{
    /** @var RecursiveValidator */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function createFromRequest(
        string $className,
        Request $request,
        bool $runValidation = true
    ): VerifiedRequest {
        $inputParams = array_merge(
            $request->query->all(),
            $request->request->all(),
            (array) json_decode(file_get_contents('php://input'), true)
        );

        if ($runValidation) {
            $this->validate($className, $inputParams);
        }

        return new $className($inputParams);
    }

    public function createFromArray(
        string $className,
        array $inputParams,
        bool $runValidation = true
    ): VerifiedRequest {
        if ($runValidation) {
            $this->validate($className, $inputParams);
        }

        return new $className($inputParams);
    }

    private function validate(string $className, array &$params)
    {
        /** @var array $fields */
        $fields = $className::getValidationRules();

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
        if ($optionalFields = $className::getOptionalFields()) {
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
