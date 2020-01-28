<?php

namespace Nullform\TGStatClient;

use Nullform\TGStatClient\Exceptions\CacheFailException;
use Nullform\TGStatClient\Exceptions\CallException;
use Nullform\TGStatClient\Exceptions\EmptyRequiredParamsException;
use Nullform\TGStatClient\Exceptions\InvalidParamsException;
use Nullform\TGStatClient\Exceptions\StatusPendingException;
use Nullform\TGStatClient\Models\CallbackInfo;
use Nullform\TGStatClient\Params\AbstractParams;
use Nullform\TGStatClient\Params\CallbackSetCallbackURLParams;
use Nullform\TGStatClient\Params\CallbackSubscribeChannelParams;
use Nullform\TGStatClient\Params\CallbackSubscribeWordParams;
use Nullform\TGStatClient\Params\CallbackSubscriptionsParams;
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
     * Last request.
     *
     * @var Request|null
     */
    protected $request;

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
     * Use cache for the next request.
     *
     * @var bool
     */
    protected $use_cache = true;

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
     * The function for logging that will be executed on every API call.
     *
     * @var callable
     */
    protected $log_function;


    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;

        $this->request = new Request();
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
     * Should caching be used for the next request?
     *
     * @param bool $use
     * @return $this
     */
    public function toggle_caching(bool $use = true): self
    {
        $this->use_cache = $use;

        return $this;
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
     * The function for logging that will be called on every TGStat API call.
     * Get or set value.
     *
     * The function takes a client instance as a parameter.
     *
     * @param callable|null $func
     * @return callable
     */
    public function logFunction(?callable $func): callable
    {
        if (!is_null($func)) {
            $this->log_function = $func;
        }

        return $this->log_function;
    }

    /**
     * Get last request.
     *
     * @return Request
     */
    public function lastRequest(): Request
    {
        return $this->request;
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
                    }
                    if ($item->forwarded_from) {
                        if ($_channel->id == $item->forwarded_from) {
                            $item->forwarded_from_channel = new Models\Channel($_channel);
                        }
                    }
                }
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Get information about Telegram publications - number of views, date of publication, content, etc...
     *
     * @param int|string $id t.me/username/123, t.me/c/1256804429/1230 or TGStat post ID.
     * @return Models\Post|null
     * @throws CacheFailException
     * @throws CallException
     * @throws EmptyRequiredParamsException
     * @throws StatusPendingException
     * @see https://api.tgstat.ru/docs/ru/posts/get.html
     */
    public function callPostsGet($id): ?Models\Post
    {
        $post = null;

        $params = new class extends AbstractParams {
            public $postId;
        };

        $params->postId = $id;

        $params->checkRequiredParams(['postId']);

        $response = $this->call('GET', 'posts/get', $params);

        $payload = $response->getPayload();

        if (is_object($payload)) {
            $post = new Models\Post($payload);
        }

        return $post;
    }

    /**
     * Get general information about the channel - link to the channel, name, description, avatar etc...
     *
     * @param int|string $id @username, t.me/username, t.me/joinchat/AAAAABbbbbcccc or TGStat channel ID.
     * @return Models\Channel|null
     * @throws CacheFailException
     * @throws CallException
     * @throws EmptyRequiredParamsException
     * @throws StatusPendingException
     * @see https://api.tgstat.ru/docs/ru/channels/get.html
     */
    public function callChannelsGet($id): ?Models\Channel
    {
        $channel = null;

        $params = new class extends AbstractParams {
            public $channelId;
        };

        $params->channelId = $id;

        $params->checkRequiredParams(['channelId']);

        $response = $this->call('GET', 'channels/get', $params);

        $payload = $response->getPayload();

        if (is_object($payload)) {
            $channel = new Models\Channel($payload);
        }

        return $channel;
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
     * Setting callback URL.
     *
     * The method allows you to set the Callback URL to which you want to receive notifications about the occurrence
     * of events of interest to you.
     *
     * If the callback URL is successfully verified, the method returns `true`. If the callback URL is set, but we were
     * unable to verify it, the method will return a string with the verification code.
     *
     * @param CallbackSetCallbackURLParams $params
     * @return bool|string
     * @throws CacheFailException
     * @throws CallException
     * @throws EmptyRequiredParamsException
     * @throws StatusPendingException
     * @see https://api.beta.tgstat.ru/docs/ru/callback/set-callback-url.html
     */
    public function callCallbackSetCallbackURL(CallbackSetCallbackURLParams $params)
    {
        $verify_code = '';

        $params->checkRequiredParams(['callback_url']);

        try {

            $this->call('POST', 'callback/set-callback-url', $params);

        } catch (CallException $exception) {

            $response = $this->lastResponse();

            if (!empty($response)) { // Retrive verify_code from response body
                $response_obj = @json_decode($response->getResponseBody());
                if (!empty($response_obj->verify_code)) { // Response body is valid JSON and contains verify_code
                    $verify_code = $response_obj->verify_code;
                }
            }

            if (empty($verify_code)) { // We can't get verify_code from response
                throw new CallException($exception->getMessage(), $exception->getCode());
            }

        }

        return !empty($verify_code) ? $verify_code : true;
    }

    /**
     * Get callback info.
     *
     * The method allows you to get information about the Callback URL set, errors that occur when accessing your
     * Callback URL and the number of unsent messages in the queue.
     *
     * @return CallbackInfo|null
     * @throws CacheFailException
     * @throws CallException
     * @throws StatusPendingException
     * @see https://api.beta.tgstat.ru/docs/ru/callback/get-callback-info.html
     */
    public function callCallbackGetCallbackInfo(): ?CallbackInfo
    {
        $info = null;

        $response = $this->call('GET', 'callback/get-callback-info');

        $payload = $response->getPayload();

        if (!empty($payload)) {
            $info = new CallbackInfo($payload);
        }

        return $info;
    }

    /**
     * Channel subscription.
     *
     * The method allows you to subscribe and receive notifications about the following events in the Telegram channel
     * at the specified Callback URL: new publication, publication editing, publication removal.
     *
     * @param CallbackSubscribeChannelParams $params
     * @return int|null Subscription ID
     * @throws CacheFailException
     * @throws CallException
     * @throws EmptyRequiredParamsException
     * @throws StatusPendingException
     * @see https://api.beta.tgstat.ru/docs/ru/callback/subscribe-channel.html
     */
    public function callCallbackSubscribeChannel(CallbackSubscribeChannelParams $params): ?int
    {
        $subscription_id = null;

        $params->checkRequiredParams(['channel_id', 'event_types']);

        $response = $this->call('POST', 'callback/subscribe-channel', $params);

        if ($response->getPayload()) {
            $subscription_id = (int)$response->getPayload()->subscription_id;
        }

        return $subscription_id;
    }

    /**
     * Keyword subscription.
     *
     * The method allows you to subscribe to a keyword (or phrase) and receive notifications on the appearance in the
     * Telegram channels of new publications in which a given word occurs on the specified Callback URL.
     *
     * @param CallbackSubscribeWordParams $params
     * @return int|null Subscription ID
     * @throws CacheFailException
     * @throws CallException
     * @throws EmptyRequiredParamsException
     * @throws StatusPendingException
     * @see https://api.beta.tgstat.ru/docs/ru/callback/subscribe-word.html
     */
    public function callCallbackSubscribeWord(CallbackSubscribeWordParams $params): ?int
    {
        $subscription_id = null;

        $params->checkRequiredParams(['q', 'event_types']);

        $response = $this->call('POST', 'callback/subscribe-word', $params);

        if ($response->getPayload()) {
            $subscription_id = (int)$response->getPayload()->subscription_id;
        }

        return $subscription_id;
    }

    /**
     * Get subscriptions list.
     *
     * The method allows you to get a list of your active subscriptions.
     *
     * @param CallbackSubscriptionsParams|null $params
     * @return Models\Subscription[]
     * @throws CacheFailException
     * @throws CallException
     * @throws StatusPendingException
     * @see https://api.beta.tgstat.ru/docs/ru/callback/subscriptions-list.html
     */
    public function callCallbackSubscriptionsList(CallbackSubscriptionsParams $params = null): array
    {
        $subscriptions = [];

        $response = $this->call('GET', 'callback/subscriptions-list', $params);

        $payload = $response->getPayload();

        if (is_object($payload) && is_array($payload->subscriptions)) {
            foreach ($payload->subscriptions as $_subscription) {
                $subscriptions[] = new Models\Subscription($_subscription);
            }
        }

        return $subscriptions;
    }

    /**
     * Unsubscribe.
     *
     * The method allows you to cancel a previously created subscription.
     *
     * @param int $subscription_id
     * @return bool
     * @throws CacheFailException
     * @throws CallException
     * @throws EmptyRequiredParamsException
     * @throws StatusPendingException
     * @see https://api.beta.tgstat.ru/docs/ru/callback/unsubscribe.html
     */
    public function callCallbackUnsubscribe(int $subscription_id): bool
    {
        $params = new class extends AbstractParams {
            public $subscription_id = 0;
        };

        $params->subscription_id = $subscription_id;

        $params->checkRequiredParams(['subscription_id']);

        $this->call('POST', 'callback/unsubscribe', $params);

        return true;
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

        // Make a new request...
        $this->request = new Request();
        $this->request->base_url = $this->baseUrl();
        $this->request->method = strtoupper($http_method);
        $this->request->path = $path;
        $this->request->params = $params;
        $this->request->sandbox = $this->sandbox();
        $this->request->user_agent = $this->userAgent();
        $this->request->timeout = $this->timeout();

        $cache_key = $this->cache_prefix . $this->request->hash();

        if ($this->request->method != 'GET') {
            $this->toggle_caching(false); // Forced switch off caching for non GET requests
        }

        if (!is_null($this->cache) && $this->use_cache) {
            $cached_response = $this->getResponseFromCache($cache_key);
            if (!empty($cached_response) && $cached_response instanceof Response) {
                // Get response from cache
                $this->response = $cached_response;
                $this->response->from_cache = true;
                $this->log();
                $this->toggle_caching(true); // Switch on caching for the next request
                return $this->response;
            }
        }

        $this->response = $this->request->send();

        $this->log();

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

        $this->toggle_caching(true); // Switch on caching for the next request

        return $this->response;
    }

    /**
     * Write TGStat API calls to log.
     *
     * The method can be overridden in your application to log any API calls.
     *
     * @return mixed
     */
    protected function log()
    {
        return is_callable($this->log_function) ? call_user_func($this->log_function, $this) : null;
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