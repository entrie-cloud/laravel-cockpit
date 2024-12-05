<?php

namespace EntrieCloud\LaravelCockpit;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

class LaravelCockpitManager
{
    protected array $connections = [];

    public function connection(string $name = null): LaravelCockpit
    {
        $name = $name ?: $this->getDefaultConnection();

        return $this->connections[$name] = $this->get($name);
    }

    public function getDefaultConnection(): string
    {
        return config('cockpit.default');
    }

    protected function get(string $name): LaravelCockpit
    {
        return $this->connections[$name] ?? $this->resolve($name);
    }

    protected function getConfig(string $name)
    {
        return config("cockpit.connections.$name") ?: [];
    }

    protected function ignoreCache(string $secret = ''): bool
    {
        if (! $secret) {
            return false;
        }

        $secrets = explode(',', request()->query(config('cockpit.cache_ignore_query')));
        $secrets = array_filter($secrets);

        return in_array($secret, $secrets, true);
    }

    protected function resolve(string $name): LaravelCockpit
    {
        $config = $this->getConfig($name);

        return new LaravelCockpit(
            connection: $name,
            apiUrl: $config['api_url'],
            apiKey: $config['api_key'],
            storageUrl: $config['storage_url'],
            cacheLifetime: $config['cache_lifetime'] ?? false,
            cacheIgnore: $this->ignoreCache((string) ($config['cache_ignore_secret'] ?? null)),
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function pruneCache(): void
    {
        if (cache()->supportsTags()) {
            cache()->tags(['laravel-cockpit.request'])->flush();

            return;
        }

        $connections = array_keys(config('cockpit.connections'));

        foreach ($connections as $connection) {
            $this->connection($connection)->flushCache();
        }
    }

    public function __call($method, $parameters): mixed
    {
        return $this->connection()->$method(...$parameters);
    }
}
