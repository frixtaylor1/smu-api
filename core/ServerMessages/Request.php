<?php

declare(strict_types=1);

namespace SMU\Core;

class Request
{
    private $method;
    private $path;
    private $params;
    private $body;

    public function __construct()
    {
        $this->method  = $_SERVER['REQUEST_METHOD'];
        $this->path    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->params  = $_GET;
        $this->body    = json_decode(file_get_contents('php://input'), true);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $key): ?array
    {
        return $this->params[$key] ?? null;
    }

    public function setParams(array $params): void
    {
        foreach ($params as $key => $value) {
            $this->params[$key] = is_array($value) ? json_encode($value) : $value;
        }
    }

    public function getHeader(string $name): ?string
    {
        return (isset(getallheaders()[$name]) ? getallheaders()[$name] : null);
    }

    public function getBody()
    {
        return $this->body;
    }
}
