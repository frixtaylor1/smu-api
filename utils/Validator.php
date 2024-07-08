<?php

declare(strict_types=1);

include_once('ValidatorConstants.php');
include_once('Request.php');

class ValidatorResponse {    
    private $schemeErrors;

    public function __construct(array $schemeErrors) {
        $this->schemeErrors = $schemeErrors;
    }

    public function thereIsErrors(): bool {
        return !$this->schemeErrors['validation_scheme']['nb_errors'] == 0;
    }

    public function getErrors(): ?array {
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

        if (count($this->validationSqueme) == 0) {
            $this->errorsCounter++;
            return [
                'validation_scheme' => [
                    'status'  => false,
                    'message' => 'No validation scheme defined'
                ]
            ];
        }

        foreach ($this->validationSqueme as $key => $scheme) {
            $value = $params[$key] ?? null;

            if (!$scheme['optional']['value']) {
                if (!$value) {
                    $this->errors[$key] = [
                        'message' => $scheme['optional']['message'] ?? 'This field is required',
                    ];
                    $this->errorsCounter++;
                    continue;
                }
            }

            $schemeValidatorType = $scheme['type']['value'];

            if ($schemeValidatorType === ValidatorConstants::TYPE_INTEGER) {
                if (is_numeric($value) && intval($value) == $value) {
                    $value = intval($value);
                }
            }

            if (gettype($value) !== $schemeValidatorType) {
                $this->errorsCounter++;
                $this->errors[$key]['type'] = [
                    'message' => $scheme['type']['message'] ?? "Invalid type, expected {$schemeValidatorType}",
                ];
            }
        }

        return new ValidatorResponse([
            'validation_scheme' => [
                'errors' => $this->errors,
                'nb_errors' => $this->errorsCounter
            ]
        ]);
    }

    private function redefinitionParamErrorMsgIn(string $param)
    {
        return "Cannot redefine type in validation for param {$param}.";
    }
}