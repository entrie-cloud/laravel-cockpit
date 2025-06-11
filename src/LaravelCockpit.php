<?php

namespace EntrieCloud\LaravelCockpit;

use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

class LaravelCockpit {
    public function __construct(
        protected string $connection,
        protected string $apiUrl,
        protected string $apiKey,
        protected string $storageUrl,
        protected mixed $cacheLifetime,
        protected bool $cacheIgnore,
    )
    {
    }

    protected function prepareApiPath(string $path): string
    {
        return str($this->apiUrl)->chopEnd('/') . str($path)->start('/');
    }

    protected function prepareApiParams(array $params): array
    {
        $locale = app()->getLocale();

        return ['lang' => $locale, 'locale' => $locale, ...$params];
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws JsonException
     */
    protected function cache(string $path, array $params, Closure $closure): array
    {
        if (! $this->cacheLifetime) {
            return $closure($this);
        }

        $cacheKey = "laravel-cockpit.request:$this->connection:" . md5($this->apiUrl . $this->apiKey . $path . json_encode($params, JSON_THROW_ON_ERROR));
        $cacheSupportsTags = cache()->supportsTags();

        $cache = $cacheSupportsTags
            ? cache()->tags(['laravel-cockpit.request', $this->connection])
            : cache();

        if ($result = $cache->get($cacheKey)) {
            return $result;
        }

        $result = $closure($this);
        $cache->put($cacheKey, $result, $this->cacheLifetime === true ? null : (int) $this->cacheLifetime);

        if ($cacheSupportsTags) {
            return $result;
        }

        $container = $cache->get("laravel-cockpit.container:$this->connection", []);
        $container[] = $cacheKey;

        $cache->put("laravel-cockpit.container:$this->connection", $container, $this->cacheLifetime === true ? null : (int) $this->cacheLifetime);

        return $result;
    }

    public function ignoreCache(): self
    {
        $this->cacheIgnore = true;

        return $this;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ConnectionException
     * @throws ContainerExceptionInterface
     * @throws JsonException
     */
    public function get(string $path, array $params = [], bool $withoutCache = false): array
    {
        if ($withoutCache || $this->cacheIgnore) {
            return $this->getWithoutCache($path, $params);
        }

        return $this->cache($path, $params, fn () => $this->getWithoutCache($path, $params));
    }

    /**
     * @throws ConnectionException
     */
    public function getWithoutCache(string $path, array $params = []): array
    {
        return Http::withToken($this->apiKey)
            ->contentType('application/json')
            ->get($this->prepareApiPath($path), $this->prepareApiParams($params))
            ->json();
    }

    /**
     * @throws ConnectionException
     */
    public function post(string $path, array $params = []): array
    {
        return Http::withToken($this->apiKey)
            ->contentType('application/json')
            ->post($this->prepareApiPath($path), $this->prepareApiParams($params))
            ->json();
    }

    /**
     * @throws ConnectionException
     */
    public function delete(string $path, array $params = []): array
    {
        return Http::withToken($this->apiKey)
            ->contentType('application/json')
            ->delete($this->prepareApiPath($path), $this->prepareApiParams($params))
            ->json();
    }

    public function storage(string $path): string
    {
        return str($this->storageUrl)->chopEnd('/') . str($path)->start('/');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function flushCache(): void
    {
        if (cache()->supportsTags()) {
            cache()->tags(['laravel-cockpit.request', $this->connection])->flush();

            return;
        }

        $container = cache()->get("laravel-cockpit.container:$this->connection", []);
        $container[] = "laravel-cockpit.container:$this->connection";

        cache()->deleteMultiple($container);
    }
}
