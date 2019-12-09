<?php

use \Nullform\TGStatClient;

require '_autoload.php';
require '_helpers.php';

$token = '';
$query = '"Jazz music"';

$client = new TGStatClient\Client($token);

$params = new TGStatClient\Params\PostsSearchParams();

$params->q = $query;
$params->hideDeleted = 1;
$params->peerType = 'channel';
$params->extended = 1;
$params->extendedSyntax = 1;
$params->limit = 3;

try {

    $posts = $client->callPostsSearch($params);

    $output = print_r($posts, true);

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