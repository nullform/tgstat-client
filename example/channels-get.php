<?php

use \Nullform\TGStatClient;

require '_autoload.php';
require '_helpers.php';

$token = '';
$id = '1045550';

$client = new TGStatClient\Client($token);

try {

    $channel = $client->callChannelsGet($id);

    $output = print_r($channel, true);

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