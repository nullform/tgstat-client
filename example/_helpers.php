<?php

use Nullform\TGStatClient;

require '_autoload.php';

if (!function_exists('tgstat_client_response_info')) {

    function tgstat_client_response_info(TGStatClient\Client $client): void
    {
        // Response info
        $status = $client->lastResponse()->status;
        $http_status = $client->lastResponse()->getHttpStatus();
        $from_cache = (int)$client->lastResponse()->from_cache;
        $error = $client->lastResponse()->getError() ? $client->lastResponse()->getError()->message : '';

        $output = "\nResponse info:\n";
        $output .= "  Status: {$status}\n";
        $output .= "  HTTP status: {$http_status}\n";
        $output .= "  From cache: {$from_cache}\n";
        $output .= "  Error: {$error}\n";

        if (php_sapi_name() != 'cli') {
            $output = "<pre>$output</pre>";
        }

        echo $output;
    }

}