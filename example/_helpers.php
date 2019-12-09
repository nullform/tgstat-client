<?php

use Nullform\TGStatClient;

require '_autoload.php';

if (!function_exists('tgstat_client_request_info')) {

    function tgstat_client_request_info(TGStatClient\Client $client): void
    {
        $request = $client->lastRequest();

        // Request info
        $request_string = $request->method . ' ' . $request->base_url . '/' . $request->path;
        $sandbox = (int)$request->sandbox;

        $output = "\nRequest info:\n";
        $output .= "  {$request_string}\n";

        if (!empty($request->params)) {
            $output .= "  Params: \n";
            foreach ($request->params as $param => $value) {
                if (!is_null($value)) {
                    $output .= "    {$param}: {$value}\n";
                }
            }
        }

        $output .= "  User-Agent: {$request->user_agent}\n";
        $output .= "  Timeout: {$request->timeout}\n";
        $output .= "  Sandbox: {$sandbox}\n";

        if (!empty($request->body)) {
            $output .= "  Body: \n{$request->timeout}\n";
        }

        if (php_sapi_name() != 'cli') {
            $output = "<pre>$output</pre>";
        }

        echo $output;
    }

}

if (!function_exists('tgstat_client_response_info')) {

    function tgstat_client_response_info(TGStatClient\Client $client): void
    {
        // Response info
        $status = $client->lastResponse()->status;
        $http_status = $client->lastResponse()->getHttpStatus();
        $from_cache = (int)$client->lastResponse()->from_cache;
        $error = $client->lastResponse()->getError() ? $client->lastResponse()->getError()->message : '';

        $output = "\nResponse info:\n";
        $output .= "  TGStat status: {$status}\n";
        $output .= "  HTTP status: {$http_status}\n";
        $output .= "  From cache: {$from_cache}\n";

        if (!empty($error)) {
            $output .= "  Error: {$error}\n";
        }

        if (php_sapi_name() != 'cli') {
            $output = "<pre>$output</pre>";
        }

        echo $output;
    }

}