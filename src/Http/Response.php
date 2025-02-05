<?php

namespace Pachuli\Web\Http;

class Response
{
    public function __construct(private string $content = "", private ?int $status = 202, private readonly ?array $headers = [])
    {

    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function sendStatus(?int $status = null): void
    {
        if ($status) $this->status = $status;
        http_response_code($this->status);
    }

    public function doShowView(): void
    {
        echo $this->content;
    }

    /**
     * @return array|null
     */
    public function getHeaders(): ?array
    {
        return $this->headers;
    }
}