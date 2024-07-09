<?php

declare(strict_types=1);

include_once('Request.php');
include_once('Response.php');
include_once('Router.php');
include_once('Validator.php');

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

        }))->middleware((function (Request $request, Response $response) {}))
    );
})();
