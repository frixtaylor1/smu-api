<?php

declare(strict_types=1);

use SMU\Core\Router;
use SMU\Core\Router\Route;
use SMU\Core\Request as Request;
use SMU\Core\Response as Response;
use SMU\Core\Validator as Validator;
use SMU\Services\User as UserServices;

(function () {
    /**
     * @name /users.
     *
     * @APIDOC
     * - @description get the users.
     *
     * - @method GET
     *
     * - @returns users
     */
    Router::get(
        (new Route('/users'))->callback((function (Request $request, Response $response) {
            /** 
             * @ExpectedParams
             * 
             * @param int id <optional>
             */
            $validator = new Validator($request);
            $validatorResponse = $validator
                ->param('id')->isOptional()->isInteger()->withMessage('Must be an integer!')
                ->validate();

            /**
             * Validation error response...
             */
            if ($validatorResponse->thereIsErrors()) {
                $response->sendValidationErrorResponse($validatorResponse);
                return;
            }

            $userService = new UserServices();
            $id      = null;
            if (isset($request->getParams()['id'])) {
                $id = (int) $request->getParams()['id'];
            }
            $result = $userService->getUsers($id);
            
            $response
                ->setHeader('Content-type', 'application/json')
                ->setStatusCode(200)
                ->setBody([
                    'message' => "Hello, World! from Usuarios endpoint!",
                    'data'  => [
                        "nombre" => $result->getName(),
                        "email"  => $result->getEmail()       
                    ]
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
     * - @description create an user.
     *
     * - @method POST
     *
     * - @returns users
     */
    Router::post(
        (new Route('/users'))->callback((function (Request $request, Response $response) {
            /**
             * @ExpectedParams
             *
             * @param string name
             * @param string email
             * @param string password
             */
            $validator = new Validator($request);
            $validatorResponse = $validator
                ->param('name')->isOptional(false)->withMessage('Must this parameter!')->isString()
                ->param('email')->isOptional(false)->withMessage('Must have this parameter')->isEmail()
                ->param('password')->isOptional(false)->withMessage('Must have this parameter')->isString()
                ->validate();

            /**
             * Validation error response...
             */
            if ($validatorResponse->thereIsErrors()) {
                $response->sendValidationErrorResponse($validatorResponse);
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
            $validator = new Validator($request);
            $validatorResponse = $validator
                ->param('id')->isOptional(false)->withMessage('Must have this parameter')->isEmail()
                ->validate();

            /**
             * Validation error response...
             */
            if ($validatorResponse->thereIsErrors()) {
                $response->sendValidationErrorResponse($validatorResponse);
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
     * - @method PUT
     *
     * - @returns message
     */    
     Router::put(
        (new Route('/users'))->callback((function (Request $request, Response $response) {
            /**
             * @ExpectedParams
             *
             * @param int id
             * @param string name <optional>
             * @param string email <optional>
             */
            $validator = new Validator($request);
            $validatorResponse = $validator
                ->param('id')->isOptional(false)->withMessage('Must have this parameter')->isEmail()
                ->validate();

            /**
             * Validation error response...
             */
            if ($validatorResponse->thereIsErrors()) {
                $response->sendValidationErrorResponse($validatorResponse);
                return;
            }
        }))->middleware((function (Request $request, Response $response) {
            // return RouterConstants::PREVENT_MAIN_CALLBACK_EXECUTION;      
        }))
    );
})();
