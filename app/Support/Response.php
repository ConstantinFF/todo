<?php

namespace Todo\Support;

class Response
{
    private $body;

    public function __construct($body = null)
    {
        $this->body = $body;
    }

    /**
     * Set response headers and return response body as json.
     *
     * @return string
     */
    public function toJson()
    {
        header('Content-Type: application/json');

        return json_encode($this->body);
    }

    /**
     * Handle responce as a string.
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->toJson();
    }

    /**
     * Return Response body.
     *
     * @return mixed
     */
    public function body()
    {
        return $this->body;
    }
}
