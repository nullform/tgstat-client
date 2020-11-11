<?php

use \Nullform\TGStatClient;

require '_autoload.php';
require '_helpers.php';

$token = '';
$id = '14083336647';

$client = new TGStatClient\Client($token);

try {

    $stat = $client->callPostsStat($id, true);

    $output = print_r($stat, true);

    if (php_sapi_name() != 'cli') {
        echo "<pre>$output</pre>";
    } else {
        echo $output;
    }

} catch (\Exception $exception) {

    echo $exception->getMessage();

}

tgstat_client_request_info($client);
tgstat_client_response_info($client);