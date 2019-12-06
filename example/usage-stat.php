<?php

use Nullform\TGStatClient;

require '_autoload.php';
require '_helpers.php';

$token = '';

$client = new TGStatClient\Client($token);
$client->sandbox(true);
$client->timeout(10);
$client->userAgent('TGStatClient');

// Set PSR-6 or PSR-16 cache instance, TTL (60) and cache keys prefix (tgstat_client_)
// $client->caching($cache, 60, 'tgstat_client_');

try {

    $stat = $client->callUsageStat();

    $output = print_r($stat, true);

    if (php_sapi_name() != 'cli') {
        echo "<pre>$output</pre>";
    } else {
        echo $output;
    }

} catch (\Exception $exception) {

    echo $exception->getMessage();

}

tgstat_client_response_info($client);