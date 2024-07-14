<?php

declare(strict_types=1);

namespace SMU\Core\Router;

use SMU\Core\Request as Request;
use SMU\Core\Response as Response;

class Route
{
    private $middlewareCallback;
    private $callback;
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Middleware must return PREVENT_MAIN_CALLBACK_EXECUTION const
     * if you want to prevent the execution of the callback method
     *
     * @params callable $callback
     *
     * @return self
     */
    public function middleware(callable $callback): self
    {
        $this->middlewareCallback = $callback;
        return $this;
    }

    /**
     * Main handler callback of the endpoint.
     *
     * @params callable $callback
     *
     * @return self
     */
    public function callback(callable $callback): self
    {
        $this->callback = $callback;
        return $this;
    }

    public function execute(Request $request, Response $response): void
    {
        if ($this->middlewareCallback) {
            $middlewareResult = call_user_func_array($this->middlewareCallback, [$request, $response]);
            if ($middlewareResult === false) {
                return;
            }
        }
        call_user_func_array($this->callback, [$request, $response]);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}