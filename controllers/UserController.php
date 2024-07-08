<?php

declare(strict_types=1);

include_once('Request.php');
include_once('Response.php');
include_once('Router.php');
include_once('Validator.php');

(function () {
    Router::get('/users', function (Request $request, Response $response) {

        $validator = new Validator($request);
        $validatorResponse = $validator
            ->param('id')
            ->isOptional(false)->withMessage('Must have this parameter')
            ->isInteger()->withMessage('Must be an integer!')
            ->param('name')
            ->isOptional(false)->withMessage('The parameter mus be in the request')
            ->isString()->withMessage('Must be an integer!')
            ->validate();
 
        if ($validatorResponse->thereIsErrors()) {
            $response
                ->setHeader('Content-Type', 'application/json')
                ->setStatusCode(403)
                ->setBody([
                    "Message" => json_encode($validatorResponse->getErrors())
                ])->send();
            return;
        }

        $response
            ->setHeader('Content-type', 'application/json')
            ->setStatusCode(200)
            ->setBody([
                'message' => "Hello, World! from Usuarios endpoint!",
                'params' => json_encode($request->getParams())
            ])->send();
    });

    Router::get('/users/{id}', function (Request $request, Response $response) {
        $validator = new Validator($request);
        $errors = $validator
            ->param('id')
            ->isOptional(false)->withMessage('Must have this parameter')
            ->isInteger()->withMessage('Must be an integer!')
            ->param('name')
            ->isOptional(false)
            ->isString()->withMessage('Must be an integer!')
            ->validate();

        if ($errors["validation_scheme"]['nb_errors']) {
            $response
                ->setHeader('Content-Type', 'application/json')
                ->setStatusCode(403)
                ->setBody([
                    "Message" => json_encode($errors)
                ])->send();
            return;
        }

        $response
            ->setHeader('Content-Type', 'application/json')
            ->setStatusCode(200)
            ->setBody([
                "Message" => "The user id is: {$request->getParam('id')}"
            ])->send();
    });
})();
