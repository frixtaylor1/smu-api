<?php

declare(strict_types=1);

use SMU\Core\Router;
use SMU\Core\Router\Route;
use SMU\Core\Request as Request;
use SMU\Core\Response as Response;
use SMU\Core\Validator as Validator;

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
             */
            $validator = new Validator($request);
            $validatorResponse = $validator
                ->param('id')->isOptional()->isInteger()->withMessage('Must be an integer!')
                ->param('user')->isOptional()->isString()->withMessage('Must be an string')
                ->validate();

            /**
             * Validation error response...
             */
            if ($validatorResponse->thereIsErrors()) {
                $response->sendValidationErrorResponse($validatorResponse);
                return;
            }

            $response
                ->setHeader('Content-type', 'application/json')
                ->setStatusCode(200)
                ->setBody([
                    'message' => "Hello, World! from Usuarios endpoint!",
                    'params'  => json_encode($request->getParams())
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
             */
            $validator = new Validator($request);
            $validatorResponse = $validator
                ->param('name')->isOptional(false)->withMessage('Must this parameter!')->isString()
                ->param('email')->isOptional(false)->withMessage('Must have this parameter')->isEmail()
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

     Router::put(
         (new Route('/users'))->callback((function (Request $request, Response $response) {
             
         }))->middleware((function (Request $request, Response $response) {
            // return RouterConstants::PREVENT_MAIN_CALLBACK_EXECUTION;      
         }))
     );
    
})();
