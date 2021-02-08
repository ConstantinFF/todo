<?php

namespace Todo\Support;

class Response
{
    private $body;

    public function __construct($body = null)
    {
        $this->body = $body;
    }

    public function toJson()
    {
        header('Content-Type: application/json');

        return json_encode($this->body);
    }

    public function __toString() : string
    {
        return $this->toJson();
    }

    public function body()
    {
        return $this->body;
    }
}
