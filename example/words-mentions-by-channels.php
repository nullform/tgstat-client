<?php

use \Nullform\TGStatClient;

require '_autoload.php';
require '_helpers.php';

$token = '';
$query = 'Jazz music';

$client = new TGStatClient\Client($token);

$params = new TGStatClient\Params\WordsMentionsByChannelsParams();

$params->q = $query;
$params->strongSearch = 1;

try {

    $posts = $client->callWordsMentionsByChannels($params);

    $output = print_r($posts, true);

    if (php_sapi_name() != 'cli') {
        echo "<pre>$output</pre>";
    } else {
        echo $output;
    }

} catch (\Exception $exception) {

    echo $exception->getMessage();

}

tgstat_client_response_info($client);