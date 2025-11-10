<?php

class HttpResponses
{
    protected function success($data, $message = null, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "Request was successful",
            "message" => $message,
            "data" => $data
        ]);
        exit;
    }

    protected function error($data, $message = null, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "An Error Occurred",
            "message" => $message,
            "data" => $data
        ]);
        exit;
    }
}