<?php

declare(strict_types=1);

include_once('Request.php');
include_once('Response.php');
include_once('Router.php');
include_once('Validator.php');

(function () {

    /**
     * @name Router Method.
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

            $validator = new Validator($request);
            $validatorResponse = $validator
                ->param('id')->isOptional(false)->withMessage('Must have this parameter')->isInteger()->withMessage('Must be an integer!')
                ->param('name')->isOptional(false)->withMessage('The parameter must be in the request')->isString()->withMessage('Must be an integer!')
                ->validate();

            if ($validatorResponse->thereIsErrors()) {
                $response
                    ->setHeader('Content-Type', 'application/json')
                    ->setStatusCode(403)
                    ->setBody([
                        "errors" => json_encode($validatorResponse->getErrors())
                    ])->send();
                return;
            }

            $host = getenv('MYSQL_HOST') ?: 'db';
            $database = getenv('MYSQL_DATABASE');
            $password = getenv('MYSQL_PASSWORD');
            
            $conn  = new mysqli($host, 'root', $password, $database, 3306);

            if ($conn->connect_error) {
                die("Connection failure: " . $conn->connect_error);
            }

            $response
                ->setHeader('Content-type', 'application/json')
                ->setStatusCode(200)
                ->setBody([
                    'message' => "Hello, World! from Usuarios endpoint!",
                    'params' => json_encode($request->getParams())
                ])->send();
        }))
        // }))->middleware((function (Request $request, Response $response) {
        //     if (!$request->getHeader('Authorization')) {

        //         /**
        //          * Logic for auth... 
        //          */
        //         $response
        //             ->setHeader('Content-Type', 'application/json')
        //             ->setStatusCode(401)
        //             ->setBody(["Message" => "Unauthorized"])
        //             ->send();
        //         return false;
        //     }
        //     return true;
        // }))
    );
})();
