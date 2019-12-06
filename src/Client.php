<?php

namespace Nullform\TGStatClient;

use Nullform\TGStatClient\Exceptions\CacheFailException;
use Nullform\TGStatClient\Exceptions\CallException;
use Nullform\TGStatClient\Exceptions\EmptyRequiredParamsException;
use Nullform\TGStatClient\Exceptions\InvalidParamsException;
use Nullform\TGStatClient\Exceptions\StatusPendingException;
use Nullform\TGStatClient\Params\AbstractParams;
use Nullform\TGStatClient\Params\FindUserByPhoneParams;
use Nullform\TGStatClient\Params\PostsSearchParams;
use Nullform\TGStatClient\Params\WordsMentionsByChannelsParams;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * TGStat API client.
 *
 * @package Nullform\TGStatClient
 */
class Client
{
    public const PRODUCTION_URL = 'https://api.tgstat.ru';
    public const SANDBOX_URL = 'https://api.tgstat.ru';
    public const DEFAULT_TIMEOUT = 10;

    /**
     * The response of the last request.
     *
     * @var Response|null
     */
    protected $response;

    /**
     * An PSR-6 or PSR-16 cache instance.
     *
     * @var CacheItemPoolInterface|CacheInterface|null
     */
    protected $cache;

    /**
     * Cache TTL.
     *
     * @var int
     */
    protected $cache_ttl = 60;

    /**
     * Prefix for cache items.
     *
     * @var string
     */
    protected $cache_prefix = 'tgstat_client_';

    /**
     * TGStat token.
     *
     * @var string
     */
    protected $token = '';

    /**
     * Is sandbox.
     *
     * @var bool
     */
    protected $is_sandbox = false;

    /**
     * Request timeout.
     *
     * @var int
     */
    protected $timeout = self::DEFAULT_TIMEOUT;

    /**
     * The 'User-Agent' request header content.
     *
     * @var string
     */
    protected $user_agent = 'nullform/tgstat-client';


    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;

        $this->response = new Response();
    }

    /**
     * Sandbox mode.
     * Get or set value.
     *
     * @param bool|null $is_sandbox New value if needed.
     * @return bool Current value.
     */
    public function sandbox(?bool $is_sandbox = null): bool
    {
        if (!is_null($is_sandbox)) {
            $this->is_sandbox = $is_sandbox;
        }

        return $this->is_sandbox;
    }

    /**
     * Timeout (maximum time the request is allowed to take).
     * Get or set value.
     *
     * @param int|null $timeout New value if needed.
     * @return int Current value.
     */
    public function timeout(?int $timeout = null): int
    {
        if (!is_null($timeout)) {
            $this->timeout = $timeout;
        }

        return $this->timeout;
    }

    /**
     * Caching of API responses.
     *
     * You can pass null to disable caching.
     *
     * @param CacheItemPoolInterface|CacheInterface|null $cache An PSR-6 or PSR-16 cache instance.
     * @param int                                        $ttl
     * @param string                                     $prefix
     * @return bool Is caching currently available (cache instance successfully set).
     */
    public function caching($cache, int $ttl, string $prefix = ""): bool
    {
        if ($cache instanceof CacheItemPoolInterface || $cache instanceof CacheInterface) {
            $this->cache = $cache;
        } else {
            $this->cache = null;
        }

        $this->cache_ttl = $ttl;
        $this->cache_prefix = $prefix;

        return !is_null($this->cache) ? true : false;
    }

    /**
     * The User-Agent request header value.
     * Get or set value.
     *
     * Default: nullform/tgstat-client
     *
     * @param string|null $user_agent New value if needed.
     * @return string Current value.
     */
    public function userAgent(?string $user_agent = null): string
    {
        if (!is_null($user_agent)) {
            $this->user_agent = $user_agent;
        }

        return $this->user_agent;
    }

    /**
     * Get last response.
     *
     * @return Response
     */
    public function lastResponse(): Response
    {
        return $this->response;
    }

    /**
     * Obtaining statistics on the use of the API at the rates you activated.
     * It will show activated services, their end date, allowed quotas and the number of requests used.
     *
     * @return Models\UsageStatItemStat[]|Models\UsageStatItemCallback[]|Models\UsageStatItemSearch[]
     * @throws CallException
     * @throws CacheFailException
     * @see https://api.tgstat.ru/docs/ru/usage/stat.html
     */
    public function callUsageStat(): array
    {
        $response = $this->call('GET', 'usage/stat');
        $items = $response->getPayload();
        $result = [];

        if (!empty($items) && is_array($items)) {
            foreach ($items as $_item) {
                if (isset($_item->spentChannels)) {
                    $result[] = new Models\UsageStatItemStat($_item);
                } elseif (isset($_item->spentObjects)) {
                    $result[] = new Models\UsageStatItemCallback($_item);
                } elseif (isset($_item->spentWords)) {
                    $result[] = new Models\UsageStatItemSearch($_item);
                }
            }
        }

        return $result;
    }

    /**
     * A method for obtaining data about mentions of a keyword / phrase grouped by channel.
     *
     * Suitable for tracking channels that are more likely to write on a given topic, mention a brand or a person
     * in Telegram publications. Returns channel information, number of references, reach and date of the last
     * mention of a keyword in the channel.
     *
     * @param WordsMentionsByChannelsParams $params
     * @return Models\MentionsByChannelsItem[]
     * @throws CallException
     * @throws CacheFailException
     * @throws EmptyRequiredParamsException
     * @see https://api.tgstat.ru/docs/ru/words/mentions-by-channels.html
     */
    public function callWordsMentionsByChannels(WordsMentionsByChannelsParams $params): array
    {
        /**
         * @var Models\MentionsByChannelsItem[] $result
         */
        $result = [];

        $params->checkRequiredParams(['q']);

        $response = $this->call('GET', 'words/mentions-by-channels', $params);

        if (!empty($response->getPayload()->items)) {
            $items = $response->getPayload()->items;
            $channels = $response->getPayload()->channels;

            foreach ($items as $_item) {
                $item = new Models\MentionsByChannelsItem($_item);
                foreach ($channels as $_channel) {
                    if ($_channel->id == $item->channel_id) {
                        $item->channel = new Models\Channel($_channel);
                        break;
                    }
                }
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Method for searching publications by keyword. Returns publications sorted in reverse chronological order
     * (last on top) in which the search text was found.
     *
     * @param PostsSearchParams $params
     * @return Models\Post[]
     * @throws CallException
     * @throws InvalidParamsException
     * @throws CacheFailException
     * @throws EmptyRequiredParamsException
     * @see https://api.tgstat.ru/docs/ru/posts/search.html
     */
    public function callPostsSearch(PostsSearchParams $params): array
    {
        /**
         * @var Models\Post[] $result
         */
        $result = [];

        $params->checkRequiredParams(['q']);
        $params->checkLimitAndOffset();

        $response = $this->call('GET', 'posts/search', $params);

        if (!empty($response->getPayload()->items)) {
            $items = $response->getPayload()->items;
            $channels = [];

            if (is_array($response->getPayload()->channels)) {
                $channels = $response->getPayload()->channels;
            }

            foreach ($items as $_item) {
                $item = new Models\Post($_item);
                foreach ($channels as $_channel) {
                    if ($_channel->id == $item->channel_id) {
                        $item->channel = new Models\Channel($_channel);
                        break;
                    }
                }
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Find Telegram users by phone number.
     *
     * @param FindUserByPhoneParams $params
     * @return Models\TelegramProfile|null If null, phone not registered.
     * @throws CacheFailException
     * @throws CallException
     * @throws EmptyRequiredParamsException
     * @throws StatusPendingException
     */
    public function callFindUserByPhone(FindUserByPhoneParams $params): ?Models\TelegramProfile
    {
        /**
         * @var Models\TelegramProfile|null $result
         */
        $profile = null;

        $params->checkRequiredParams(['phone']);

        $response = $this->call('GET', 'tools/find-user-by-phone', $params);

        $payload = $response->getPayload();

        if (is_object($payload)) {
            $profile = new Models\TelegramProfile($payload);
        }

        return $profile;
    }

    /**
     * Calling an TGStat API method.
     *
     * @param string              $http_method GET or POST
     * @param string              $path        Example: usage/stat
     * @param AbstractParams|null $params
     * @return Response
     * @throws CallException
     * @throws CacheFailException
     * @throws StatusPendingException
     */
    protected function call(string $http_method, string $path, ?AbstractParams $params = null): Response
    {
        if (strpos($path, '/') === 0) {
            $path = substr($path, 1);
        }

        if (is_null($params)) {
            $params = new class extends AbstractParams {
                public $token = '';
            };
        }

        $params->token = $this->token;

        $cache_key = $this->cache_prefix .
            md5(
                serialize(
                    [
                        $http_method,
                        $path,
                        (array)$params,
                        $this->sandbox()
                    ]
                )
            );

        $params_string = $params->toString();
        $endpoint = $this->baseUrl() . '/' . $path;
        $allowed_http_methods = ['GET', 'POST'];
        $http_method = strtoupper($http_method);

        $this->response = new Response(); // Reset last response

        if (!in_array($http_method, $allowed_http_methods)) {
            throw new Exceptions\CallException('HTTP method not alowed');
        }

        if (!is_null($this->cache)) {
            $cached_response = $this->getResponseFromCache($cache_key);
            if (!empty($cached_response) && $cached_response instanceof Response) {
                $this->response = $cached_response;
                $this->response->from_cache = true;
                return $this->response;
            }
        }

        $curl_options = [
            CURLOPT_URL            => $endpoint . (!empty($params_string) ? '?' . $params_string : ''),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => $this->timeout(),
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_USERAGENT      => $this->userAgent(),
        ];

        if ($http_method === 'POST') {
            $curl_options[CURLOPT_POST] = true;
        }

        $ch = curl_init();

        curl_setopt_array($ch, $curl_options);

        $response_body = (string)curl_exec($ch);

        $curl_info = curl_getinfo($ch);

        $this->response->setResponseBody($response_body);
        $this->response->setHttpStatus((int)$curl_info['http_code']);

        if ($this->response->status == Response::STATUS_ERROR || empty($this->response->status)) {
            if ($this->response->getError()) {
                throw new CallException('Telegram Analytics server error: ' . $this->response->getError()->message);
            } else {
                throw new CallException('Telegram Analytics server is not responding');
            }
        } elseif ($this->response->status == Response::STATUS_PENDING) {
            throw new StatusPendingException('Request pending... Please try later');
        }

        if (!is_null($this->cache) && $this->response->status == Response::STATUS_OK) {
            $this->storeResponseToCache($cache_key);
        }

        return $this->response;
    }

    /**
     * Store response to cache.
     *
     * @param string $key
     * @return bool
     * @throws CacheFailException
     */
    protected function storeResponseToCache(string $key): bool
    {
        $stored = false;

        try {
            if ($this->cache instanceof CacheItemPoolInterface) { // PSR-6
                $item = $this->cache->getItem($key);
                $item->set($this->response);
                $item->expiresAfter($this->cache_ttl);

                $stored = $this->cache->save($item);
            } elseif ($this->cache instanceof CacheInterface) { // PSR-16
                $stored = $this->cache->set($key, $this->response, $this->cache_ttl);
            }
        } catch (\Psr\Cache\CacheException | \Psr\SimpleCache\CacheException $exception) {
            throw new CacheFailException($exception->getMessage());
        }

        return $stored;
    }

    /**
     * Get response from cache;
     *
     * @param string $key
     * @return Response|null
     * @throws CacheFailException
     */
    protected function getResponseFromCache(string $key): ?Response
    {
        $response = null;

        try {
            if ($this->cache instanceof CacheItemPoolInterface) { // PSR-6
                if ($this->cache->hasItem($key)) {
                    $response = $this->cache->getItem($key)->get();
                }
            } elseif ($this->cache instanceof CacheInterface) { // PSR-16
                $response = $this->cache->get($key);
            }
        } catch (\Psr\Cache\CacheException | \Psr\SimpleCache\CacheException $exception) {
            throw new CacheFailException($exception->getMessage());
        }

        if (!is_null($response) && !($response instanceof Response)) { // WTF
            throw new CacheFailException('Invalid cached response');
        }

        return $response;
    }

    /**
     * Base API URL.
     *
     * @return string
     */
    protected function baseUrl(): string
    {
        return $this->sandbox() ? self::SANDBOX_URL : self::PRODUCTION_URL;
    }
}