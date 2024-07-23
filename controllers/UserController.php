<?php

declare(strict_types=1);

use SMU\Core\Router;
use SMU\Core\Router\Route;
use SMU\Core\Request;
use SMU\Core\Response;
use SMU\Core\Validator;
use SMU\Core\ValidatorResult;
use SMU\Services\User as UserServices;

(function () {
    /**
     * @name /users.
     *
     * @APIDOC
     * - get the users.
     * - @method GET
     *
     * - @return array
     */
    Router::get(
        (new Route('/users'))->callback((function (Request $request, Response $response) {
            $validatorResult = _validateParamsGetUsers($request);

            /**
             * Validation error response...
             */
            if ($validatorResult->getResponse()->thereIsErrors()) {
                $response->sendValidationErrorResponse($validatorResult->getResponse());
                return;
            }

            $userService = new UserServices();
            $users       = $userService->getUsers(
                $validatorResult->getParam('id'), 
                $validatorResult->getParam('nb_of_rows'), 
                $validatorResult->getParam('offset')
            );

            $response
                ->setHeader('Content-type', 'application/json')
                ->setStatusCode(200)
                ->setBody([
                    'users' => !is_array($users) ? $users : $users,
                ])->send();
        }))->middleware((function (Request $request, Response $response) {
            // $response
            //     ->setHeader('Content-Type', 'application/json')
            //     ->setStatusCode(401)
            //     ->setBody(["error" => "bad credentials"])
            //     ->send();
            // return RouterConstants::PREVENT_MAIN_CALLBACK_EXECUTION;
        }))
    );

    /**
     * @name /users
     *
     * @APIDOC
     * - create an user.
     * 
     * - @method POST
     *
     * - @return array
     */
    Router::post(
        (new Route('/users'))->callback((function (Request $request, Response $response) {
            $validatorResult = _validateParamsPostUsers($request);

            /**
             * Validation error response...
             */
            if ($validatorResult->getResponse()->thereIsErrors()) {
                $response->sendValidationErrorResponse($validatorResult->getResponse());
                return;
            }

            $userService    = new UserServices();
            $resUserService = $userService->createUser($validatorResult);
            $statusCode     = $resUserService['status'] ? 200 : 400;
            
            $response
                ->setHeader('Content-Type', 'application/json')
                ->setStatusCode($statusCode)
                ->setBody($resUserService)
                ->send();

        }))->middleware((function (Request $request, Response $response) {
            // return RouterConstants::PREVENT_MAIN_CALLBACK_EXECUTION;
        }))
    );

    /**
     * @name /users
     *
     * @APIDOC
     * - @description remove an user.
     *
     * - @method DELETE
     *
     * - @returns message
     */
    Router::delete(
        (new Route('/users'))->callback((function (Request $request, Response $response) {
            /**
             * @ExpectedParams
             *
             * @param int id
             */
            $validator       = new Validator($request);
            $validatorResult = $validator
                ->param('id')->isOptional(false)->withMessage('Must have this parameter')->isEmail()
                ->validate();

            /**
             * Validation error response...
             */
            if ($validatorResult->getResponse()->thereIsErrors()) {
                $response->sendValidationErrorResponse($validatorResult->getResponse());
                return;
            }
        }))->middleware((function (Request $request, Response $response) {
            // return RouterConstants::PREVENT_MAIN_CALLBACK_EXECUTION;
        }))
    );

    /**
     * @name /users
     *
     * @APIDOC
     * - @description remove an user.
     *
     * - @method PATCH
     *
     * - @returns message
     */
    Router::patch(
        (new Route('/users'))->callback((function (Request $request, Response $response) {
            /**
             * @ExpectedParams
             *
             * @param int id
             * @param string name <optional>
             * @param string email <optional>
             */
            $validator = new Validator($request);
            $validationResult = $validator
                ->only(['id'])
                ->param('id')->isOptional(false)->withMessage('Must have this parameter')->isInteger()
                ->validate();

            /**
             * Validation error response...
             */
            if ($validationResult->getResponse()->thereIsErrors()) {
                $response->sendValidationErrorResponse($validationResult->getResponse());
                return;
            }
        }))->middleware((function (Request $request, Response $response) {
            // return RouterConstants::PREVENT_MAIN_CALLBACK_EXECUTION;      
        }))
    );
})();

/**
 * Private functions...
 */ 

/** 
 * @ExpectedParams in query Request
 * 
 * @param int id          <optional>
 * @param int nb_of_rows  <optional>
 * @param int offset      <optional>
 * 
 * @return ValidatorResult
 */
function _validateParamsGetUsers(Request $request): ValidatorResult
{
    $validator = new Validator($request);
    return $validator
        ->only(['id', 'offset'])
        ->param('id')->isOptional()->isInteger()->withMessage('Must be an integer!')
        ->param('offset')->isOptional()->isInteger()->withMessage('Must be an integer!')
        ->validate();
}

/**
 * @ExpectedParams in body Request
 *
 * @param string email
 * @param string password
 * @param string name
 * @param string address
 * 
 * @return ValidatorResult
 */
function _validateParamsPostUsers(Request $request): ValidatorResult
{
    $validator = new Validator($request);
    return $validator
        ->only(['email', 'password', 'name', 'address'])
        //Query params..
        
        //Body params...
        ->param('email')->isOptional(false)->withMessage('Must have this parameter')->isEmail()
        ->param('password')->isOptional(false)->withMessage('Must have this parameter')->isString()
        ->param('name')->isOptional(false)->withMessage('Must have this parameter')->isString()
        ->param('address')->isOptional(false)->withMessage('Must have this parameter')->isString()
        ->validate();
}

/**
 * @ExpectedParams in query Request
 *
 * @param int id
 * 
 * @return ValidatorResult
 */
function _validateParamsDeleteUsers(Request $request): ValidatorResult
{
    $validator = new Validator($request);
    return $validator
        ->only(['id'])
        ->param('id')->isOptional(false)->withMessage('Must have this parameter!')->isInteger()
        ->validate();
}
