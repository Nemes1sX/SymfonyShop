<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class FormRequest
{
    protected Request $request;
    protected ValidatorInterface $validator;

    public function __construct(Request $request, ValidatorInterface $validator)
    {
        $this->request = $request;
        $this->validator = $validator;

        if (!$this->authorize()) {
            throw new AccessDeniedHttpException('This action is unauthorized.');
        }
    }

    abstract public function rules(): array;

    public function authorize(): bool
    {
        return true;
    }

    public function validated(): array
    {
        $data = $this->request->toArray();

        $violations = $this->validator->validate($data, new \Symfony\Component\Validator\Constraints\Collection($this->rules()));

        if (count($violations) > 0) {
            throw new BadRequestHttpException($this->formatErrors($violations));
        }

        // Return only the validated keys
        return array_intersect_key($data, $this->rules());
    }

    protected function formatErrors(ConstraintViolationListInterface $violations): string
    {
        $messages = [];
        foreach ($violations as $violation) {
            $messages[] = sprintf("%s: %s", $violation->getPropertyPath(), $violation->getMessage());
        }
        return implode("\n", $messages);
    }
}

