<?php

namespace Todo\Controllers;

use Todo\Support\Response;

class Error
{
    /**
     * Error response.
     *
     * @param array $request
     * @return Response
     */
    public function __invoke($exception)
    {
        http_response_code(400);
        
        return new Response(['error' => $exception->getMessage()]);
    }
}
