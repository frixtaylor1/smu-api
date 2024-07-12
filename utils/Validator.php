<?php

declare(strict_types=1);

include_once('ValidatorConstants.php');

use SMU\Request;

class ValidatorResponse
{
    private $schemeErrors;

    public function __construct(array $schemeErrors)
    {
        $this->schemeErrors = $schemeErrors;
    }

    public function thereIsErrors(): bool
    {
        return !$this->schemeErrors['validation_scheme']['nb_errors'] == 0 || $this->schemeErrors['validation_scheme']['status'] === false;
    }

    public function getErrors(): ?array
    {
        return $this->schemeErrors['validation_scheme']['errors'];
    }
}

class Validator
{
    private $request;
    private $validationSqueme = [];
    private $lastParamName;
    private $lastIndexScheme;
    private $errorsCounter = 0;
    private $errors = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function param(string $name): ?self
    {
        if (isset($this->validationSqueme[$name])) {
            throw new Error("You cannot have 2 validations for the same param with name: {$name}");
        }

        $this->lastParamName = $name;
        return $this;
    }

    public function isBoolean(): ?self
    {
        if (isset($this->validationSqueme[$this->lastParamName]['type'])) {
            throw new Error($this->redefinitionParamErrorMsgIn($this->lastParamName));
        }

        $this->lastIndexScheme = 'type';
        $this->validationSqueme[$this->lastParamName]['type'] = ['value' => ValidatorConstants::TYPE_BOOLEAN];
        return $this;
    }

    public function isInteger(): ?self
    {
        if (isset($this->validationSqueme[$this->lastParamName]['type'])) {
            throw new Error($this->redefinitionParamErrorMsgIn($this->lastParamName));
        }

        $this->lastIndexScheme = 'type';
        $this->validationSqueme[$this->lastParamName]['type'] = ['value' => ValidatorConstants::TYPE_INTEGER];
        return $this;
    }

    public function isString(): ?self
    {
        if (isset($this->validationSqueme[$this->lastParamName]['type'])) {
            throw new Error($this->redefinitionParamErrorMsgIn($this->lastParamName));
        }

        $this->lastIndexScheme = 'type';
        $this->validationSqueme[$this->lastParamName]['type'] = ['value' => ValidatorConstants::TYPE_STRING];
        return $this;
    }

    public function isArray(): ?self
    {
        if (isset($this->validationSqueme[$this->lastParamName]['type'])) {
            throw new Error($this->redefinitionParamErrorMsgIn($this->lastParamName));
        }

        $this->lastIndexScheme = 'type';
        $this->validationSqueme[$this->lastParamName]['type'] = ['value' => ValidatorConstants::TYPE_ARRAY];
        return $this;
    }

    public function isEmail(): ?self
    {
        if (isset($this->validationSqueme[$this->lastParamName]['type'])) {
            throw new Error($this->redefinitionParamErrorMsgIn($this->lastParamName));
        }

        $this->lastIndexScheme = 'type';
        $this->validationSqueme[$this->lastParamName]['type'] = ['value' => ValidatorConstants::TYPE_EMAIL];
        return $this;
    }

    public function isOptional(bool $value = true): ?self
    {
        if (isset($this->validationSqueme[$this->lastParamName]['optional'])) {
            throw new Error('You cannot redefine the optional flag twice.');
        }
        $this->lastIndexScheme = 'optional';
        $this->validationSqueme[$this->lastParamName]['optional'] = ['value' => $value];
        return $this;
    }

    public function withMessage(string $message): ?self
    {
        if (isset($this->validationSqueme[$this->lastParamName][$this->lastIndexScheme]['message'])) {
            throw new Error("You cannot redefine the Error Message in param: {$this->lastParamName} for: {$this->lastIndexScheme}");
        }

        $this->validationSqueme[$this->lastParamName][$this->lastIndexScheme]['message'] = $message;
        return $this;
    }

    public function validate(): ValidatorResponse
    {
        $params = $this->request->getParams();

        if (count($this->request->getParams()) > count($this->validationSqueme)) {
            return new ValidatorResponse([
                'validation_scheme' => [
                    'status'    => false,
                    'errors'    => ['There\'s  more params than the validation scheme.'],
                    'nb_errors' => 1
                ]
            ]);
        }

        if (count($this->validationSqueme) == 0) {
            $this->errorsCounter++;
            return new ValidatorResponse([
                'validation_scheme' => [
                    'status'    => false,
                    'errors'    => 'No validation scheme defined',
                    'nb_errors' => 1
                ]
            ]);
        }

        foreach ($this->validationSqueme as $key => $scheme) {
            $value               = $params[$key] ?? null;
            $type                = gettype($value);
            $isOptional          = $scheme['optional']['value'];
            $schemeValidatorType = $scheme['type']['value'];

            if ($isOptional && !$value && !isset($params[$key])) {
                continue;
            }

            if (!$isOptional) {
                if (!$value) {
                    $this->errors[$key] = [
                        'message' => $scheme['optional']['message']
                    ];
                    $this->errorsCounter++;
                    continue;
                }
            }

            if ($schemeValidatorType === ValidatorConstants::TYPE_INTEGER) {
                if (is_numeric($value) && intval($value) == $value) {
                    $value = intval($value);
                    $type  = ValidatorConstants::TYPE_INTEGER;
                }
            }

            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $type = ValidatorConstants::TYPE_EMAIL;
            }

            if ($type !== $schemeValidatorType || (isset($params[$key])) && !$value) {
                $this->errorsCounter++;
                $this->errors[$key]['type'] = [
                    'message' => $scheme['type']['message'] ?? "Invalid type, expected {$schemeValidatorType}",
                ];
            }
        }

        return new ValidatorResponse([
            'validation_scheme' => [
                'status'    => true,
                'errors'    => $this->errors,
                'nb_errors' => $this->errorsCounter
            ]
        ]);
    }

    private function redefinitionParamErrorMsgIn(string $param)
    {
        return "Cannot redefine type in validation for param {$param}.";
    }
}
