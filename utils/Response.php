<?php

declare(strict_types=1);

class Response
{
    private $statusCode;
    private $headers;
    private $body;

    public function __construct()
    {
        $this->statusCode = 200;
        $this->headers    = [];
    }

    public function setStatusCode(int  $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[] = [$name, $value];
        return $this;
    }

    public function setBody(array $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function send(): void
    {
        foreach ($this->getHeaders() as $header) {
            header("{$header[0]}: {$header[1]}");
        }
        http_response_code($this->getStatusCode());
        echo json_encode($this->getBody());
        return;
    }
}
