<?php

use \Nullform\TGStatClient;

require '_autoload.php';
require '_helpers.php';

$token = '';
$id = '10830972566';

$client = new TGStatClient\Client($token);

try {

    $post = $client->callPostsGet($id);

    $output = print_r($post, true);

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