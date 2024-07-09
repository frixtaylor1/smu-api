<?php

declare(strict_types=1);

include_once('Request.php');
include_once('Response.php');

class Route
{
    private $middlewareCallback;
    private $callback;
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function middleware(callable $callback): self
    {
        $this->middlewareCallback = $callback;
        return $this;
    }

    public function callback(callable $callback): self
    {
        $this->callback = $callback;
        return $this;
    }

    public function execute(Request $request, Response $response): void
    {
        if ($this->middlewareCallback) {
            $middlewareResult = call_user_func_array($this->middlewareCallback, [$request, $response]);
            if (!$middlewareResult) {
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