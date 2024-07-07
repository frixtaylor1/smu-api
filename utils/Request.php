<?php

declare(strict_types=1);

class Request
{
    private $method;
    private $path;
    private $params;
    private $body;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->params = $_GET;
        $this->body   = json_decode(file_get_contents('php://input'), true);
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $key)
    {
        return $this->params[$key] ?? null;
    }

    public function setParams(array $params)
    {
        foreach ($params as $key => $value) {
            $this->params[$key] = is_array($value) ? json_encode($value) : $value;
        }
    }

    public function getBody()
    {
        return $this->body;
    }
}
