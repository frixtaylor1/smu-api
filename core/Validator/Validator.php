<?php

declare(strict_types=1);

namespace SMU\Core;

use SMU\Core\Constants\ValidatorConstants;
use SMU\Core\Request;
use Error;

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

class ValidatorResult
{
    private $validatedParams;
    private $response;

    public function __construct(array $validatedParams, ValidatorResponse $response)
    {
        $this->validatedParams = $validatedParams;
        $this->response        = $response;
    }

    public function getParams(): array
    {
        return $this->validatedParams;
    }

    public function getResponse(): ValidatorResponse
    {
        return $this->response;
    }

    public function getParam(string $key): mixed
    {
        return isset($this->validatedParams[$key]) ? $this->validatedParams[$key] : null;
    }
}

class Validator
{
    private $request;
    private $lastParamName;
    private $lastIndexScheme;
    private $onlyParams         = [];
    private $exceptParams       = [];
    private $validationSqueme   = [];
    private $errors             = [];
    private $errorsCounter      = 0;

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
        return $this->setType(ValidatorConstants::TYPE_BOOLEAN);
    }

    public function isInteger(): ?self
    {
        return $this->setType(ValidatorConstants::TYPE_INTEGER);
    }

    public function isString(): ?self
    {
        return $this->setType(ValidatorConstants::TYPE_STRING);
    }

    public function isArray(): ?self
    {
        return $this->setType(ValidatorConstants::TYPE_ARRAY);
    }

    public function isEmail(): ?self
    {
        return $this->setType(ValidatorConstants::TYPE_EMAIL);
    }

    public function isOptional(bool $value = true): ?self
    {
        if (isset($this->validationSqueme[$this->lastParamName]['optional'])) {
            throw new Error('You cannot redefine the optional flag twice.');
        }
        $this->exceptParams[] = $this->lastParamName;
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
    
    /**
     * @todo  MUST IMPLEMENT only method for params query and body validator...  
     */
    public function only(array $acceptedParams): self
    {
        $this->onlyParams = $acceptedParams;
        return $this;
    }
  
    public function validate(): ValidatorResult
    {
        $validatedParams        = [];
        $params                 = $this->getParamsToValidate();
        $responsePreValidation  = $this->preValidationSchemeAndParams($params);

        if ($responsePreValidation) {
            return $responsePreValidation;
        }
        
        if ($params) {
            foreach ($this->validationSqueme as $key => $scheme) {
                $value = $params[$key] ?? null;
                $isOptional = $scheme['optional']['value'];
                $expectedType = $scheme['type']['value'];
        
                if ($isOptional && $value === null && !isset($params[$key])) {
                    continue;
                }
        
                if (!$isOptional && $value === null) {
                    $this->errors[$key] = [
                        'message' => $scheme['optional']['message']
                    ];
                    $this->errorsCounter++;
                    continue;
                }
        
                $actualType = gettype($value);
                $this->castTypes($expectedType, $value, $actualType);
        
                if ($actualType !== $expectedType || ($actualType === 'NULL' && isset($params[$key]))) {
                    $this->errors[$key]['type'] = [
                        'message' => $scheme['type']['message'] ?? "Invalid type, expected {$expectedType}",
                    ];
                    $this->errorsCounter++;
                    continue;
                }
        
                $validatedParams[$key] = $value;
            }
        }
        
        $response = new ValidatorResponse([
            'validation_scheme' => [
                'status'    => $this->errorsCounter === 0,
                'errors'    => $this->errors,
                'nb_errors' => $this->errorsCounter
            ]
        ]);

        return new ValidatorResult($validatedParams, $response);
    }
    
    private function setType(string $type): self
    {
        $this->evaluateTypeTwice();
        $this->lastIndexScheme = 'type';
        $this->validationSqueme[$this->lastParamName]['type'] = ['value' => $type];
        return $this;
    }
    
    private function evaluateTypeTwice($schemeType = 'type'): void
    {       
        if (isset($this->validationSqueme[$this->lastParamName][$schemeType])) {
            throw new Error($this->redefinitionParamErrorMsgIn($this->lastParamName));
        }    
    }
 
    private function redefinitionParamErrorMsgIn(string $param)
    {
        return "Cannot redefine type in validation for param {$param}.";
    }
    
    private function getParamsToValidate(): ?array
    {       
        $bodyReq  = $this->request->getBody();
        $queryReq = $this->request->getParams();
        $params   = [];

        if ($bodyReq && $queryReq){
            $params = array_merge(...$queryReq, ...$bodyReq);
        }

        if ($queryReq) {
            $params = $queryReq;
        }

        if ($bodyReq) {
            $params = $bodyReq;
        }    

        return $params;
    }
    
    private function castTypes($schemeValidatorType, & $value, & $type): void
    {
        if ($schemeValidatorType === ValidatorConstants::TYPE_INTEGER) {
            if (is_numeric($value) && intval($value) == $value) {
                $value = intval($value);
                $type  = ValidatorConstants::TYPE_INTEGER;
            }
        }

        if ($schemeValidatorType === ValidatorConstants::TYPE_EMAIL) {
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $type = ValidatorConstants::TYPE_EMAIL;
            }
        }
    }

    private function preValidationSchemeAndParams($params): ?ValidatorResult
    {
        $errorMessage = '';   

        if (count($params) > count($this->validationSqueme)) {
            $errorMessage = 'There\'s  more params than the validation scheme.';           
        }

        if (count($this->validationSqueme) == 0) {
            $errorMessage = 'No validation scheme defined';
        }

        if (count($this->onlyParams)) {
            $paramKeys = array_keys($params);            
            if ($this->onlyParams !== $paramKeys) {
                $diffParam = array_diff($paramKeys, $this->onlyParams);
                foreach ($diffParam as $param) {
                    if (!in_array($param, $this->exceptParams)) {
                        $errorMessage = 'The params aren\'t correct';
                    }
                }
            }
        }

        if ($errorMessage !== '') {
             $response = new ValidatorResponse([
                'validation_scheme' => [
                    'status'    => false,
                    'errors'    => [$errorMessage],
                    'nb_errors' => 1
                ]
            ]);           
            return new ValidatorResult([], $response);
        }

        return null;
    }
}
