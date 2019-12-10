# TGStat API client

Non official PHP client for TGStat API (https://tgstat.ru).

Documentation for working with the Telegram Analytics API:
https://api.tgstat.ru/docs/

## Installation

```
composer require nullform/tgstat-client
```

You can get your personal token here: https://api.tgstat.ru/docs/ru/start/token.html

## Usage

### Basic

```php
use Nullform\TGStatClient;

$client = new TGStatClient\Client($token);
$client->sandbox(true);
$client->timeout(10);
$client->userAgent('TGStatClient');

try {
    // API request statistics
    $usage_stat = $client->callUsageStat();
} catch (\Exception $exception) {
    $error = $exception->getMessage();
}
```

### Usage statistics

```php
$usage_stat = $client->callUsageStat();
```

### Search for posts

```php
$params = new TGStatClient\Params\PostsSearchParams();

$params->q = 'Jazz | "Jazz music"'; // Extended syntax
$params->hideDeleted = 1;
$params->peerType = 'channel';
$params->extended = 1;
$params->extendedSyntax = 1;
$params->limit = 3;

$posts = $client->callPostsSearch($params);
```

### Words mentions by channels

```php
$params = new TGStatClient\Params\WordsMentionsByChannelsParams();

$params->q = 'Jazz music';
$params->strongSearch = 1;

$mentions = $client->callWordsMentionsByChannels($params);
```

### Last request info

You can allways get last request instance by `Client::lastRequest()`.

```php
$request = $client->lastRequest();
```

### Last response info

You can allways get last response instance by `Client::lastResponse()`.

```php
$status = $client->lastResponse()->status;
$http_status = $client->lastResponse()->getHttpStatus();
$is_from_cache = $client->lastResponse()->from_cache;

if ($client->lastResponse()->getError()) {
    $error = $client->lastResponse()->getError()->message;
}
```

### Caching

You can cache API responses if you use PSR-6 or PSR-16 caching in your project.
Just pass to `Client::caching()` your cache repository instance, TTL and prefix.

If the cache instance is passed, each successful response will be stored to the cache for the `$ttl` seconds.
With repeated requests with the same parameters, the response will be taken from the cache.

```php
// Set PSR-6 or PSR-16 cache instance, TTL (60) and cache keys prefix (tgstat_client_)
$client->caching($cache, 60, 'tgstat_client_');
```

### Logging

You can log your API calls by passing your own function to `Client::logFunction()`. Passed function will be called on every TGStat API call.
The function takes an instance of `\Nullform\TGStatClient\Client` as a parameter.
For example:

```php
$log_func = function (TGStatClient\Client $client) {
    file_put_contents('tgstat-client-log.log', print_r($client->lastResponse(), true));
};

$client = new TGStatClient\Client($token);
$client->logFunction($log_func);

$stat = $client->callUsageStat();
```

Or you can just override the `Client::log()` method that is called on every TGStat API call.