# Laravel Cockpit Integration Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/entrie-cloud/laravel-cockpit.svg?style=flat-square)](https://packagist.org/packages/entrie-cloud/laravel-cockpit)
[![Total Downloads](https://img.shields.io/packagist/dt/entrie-cloud/laravel-cockpit.svg?style=flat-square)](https://packagist.org/packages/entrie-cloud/laravel-cockpit)

A Laravel package designed to seamlessly integrate with [Cockpit](https://getcockpit.com/), providing tools to interact with its API, manage connections, and improve performance with advanced caching mechanisms.

## Installation

You can install the package via composer:

```bash
composer require entrie-cloud/laravel-cockpit
```

Env variables to work out of the box:

```dotenv
COCKPIT_API_URL=
COCKPIT_API_KEY=
COCKPIT_STORAGE_URL=
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="cockpit-config"
```

This is the contents of the published config file:

```php
return [

    // The default connection name to use for Cockpit API calls.
    'default' => env('COCKPIT_CONNECTION', 'main'),

    // The URL path prefix to trigger cache clearing for Cockpit cache.
    'cache_clear_path' => env('COCKPIT_CACHE_CLEAR_PATH', '/cockpit-cache-clear'),

    // The query parameter name used to bypass caching when accessing Cockpit API routes.
    'cache_ignore_query' => env('COCKPIT_CACHE_IGNORE_QUERY', 'cockpit-cache-ignore'),

    // List of available connections to Cockpit instances.
    'connections' => [

        // The main connection configuration for interacting with Cockpit.
        'main' => [
            // The base URL of the Cockpit API.
            'api_url' => env('COCKPIT_API_URL'),

            // The API key for authenticating requests to the Cockpit API.
            'api_key' => env('COCKPIT_API_KEY'),

            // The URL to access Cockpit's storage, such as uploads or assets.
            'storage_url' => env('COCKPIT_STORAGE_URL'),

            // Lifetime of the cache in seconds. If set to `true`, caching is enabled indefinitely.
            'cache_lifetime' => env('COCKPIT_CACHE_LIFETIME', true),

            // Secret key for ignoring cache when querying Cockpit via `cache_ignore_query`.
            'cache_ignore_secret' => env('COCKPIT_CACHE_IGNORE_SECRET'),

            // Secret key for clearing Cockpit cache via `cache_clear_path`.
            'cache_clear_secret' => env('COCKPIT_CACHE_CLEAR_SECRET'),
        ],

    ],

];
```

## Usage

### Basic usage

```php
// Also have alias `LaravelCockpit`
use EntrieCloud\LaravelCockpit\Facades\LaravelCockpit;

// GET request
$data = LaravelCockpit::get('/path/to/resource');
$data = LaravelCockpit::get('/path/to/resource', ['sort' => ['order' => -1]]);

// POST request
$result = LaravelCockpit::post('/path/to/resource', ['email' => 'john.doe@example.org']);

// DELETE request
$result = LaravelCockpit::delete('/path/to/resource');
```

### Without cache

```php
// GET request without caching
$data = LaravelCockpit::getWithoutCache('/path/to/resource');
$data = LaravelCockpit::ignoreCache()->get('/path/to/resource');
```

### Request on specific connection

```php
$data = LaravelCockpit::connection('secondary')->get('/path/to/resource');
$result = LaravelCockpit::connection('secondary')->post('/path/to/resource');
$result = LaravelCockpit::connection('secondary')->delete('/path/to/resource');
```

### Clear cache

```php
// Clear cache for default connection
LaravelCockpit::flushCache();

// Clear cache for specific connection
LaravelCockpit::connection('secondary')->flushCache();

// Clear cache for all connections
LaravelCockpit::pruneCache();
```

### Storage

```php
$data = LaravelCockpit::get('/path/to/resource');

// Return full url to resource
$avatarUrl = LaravelCockpit::storage($data['avatar'])

// Return full url to resource on specific connection
$avatarUrl = LaravelCockpit::connection('secondary')->storage($data['avatar'])
```

### Ignore cache by URL query

In some cases, you may need to bypass the cache for specific Cockpit connections.
<br>
For example, during live previews of changes made in Cockpit.

To enable this, set the `COCKPIT_CACHE_IGNORE_SECRET` in your `.env` file:

```dotenv
COCKPIT_CACHE_IGNORE_SECRET=xxx-secret-xxx
```

Now, your `Laravel` application will handle requests with the query parameter `?cockpit-cache-ignore=xxx-secret-xxx` and instruct `LaravelCockpit` to skip caching for the connection matching the provided secret:

```
https://example.org/page/cool?cockpit-cache-ignore=xxx-secret-xxx
```

To instruct `LaravelCockpit` to skip caching for multiple connections you should separate `secrets` with comma(`,`) in URL Query.

```
https://example.org/page/cool?cockpit-cache-ignore=xxx-secret-xxx,xxx-secret-secondary-xxx
```

### Clear cache by URL path

Sometimes, you may need to clear the cache for specific Cockpit connections, such as after making changes to content in Cockpit.

To enable this, set the `COCKPIT_CACHE_CLEAR_SECRET` in your `.env` file:

```dotenv
COCKPIT_CACHE_CLEAR_SECRET=xxx-secret-xxx
```

With this configuration, your `Laravel` application will handle requests to the `/cockpit-cache-clear/xxx-secret-xxx` path, instructing `LaravelCockpit` to clear the cache for the matching connection:

```
https://example.org/cockpit-cache-clear/xxx-secret-xxx
```

To clear the cache for multiple connections simultaneously, separate the `secrets` with a comma(`,`) in the URL query:

```
https://example.org/cockpit-cache-clear/xxx-secret-xxx,xxx-secret-secondary-xxx
```

Behavior
- **Browser Request:** After visiting the `/cockpit-cache-clear` path, you will be redirected back to the previous page.
- **AJAX Request:** When making an AJAX request to the `/cockpit-cache-clear` path, a `204 HTTP status` is returned.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Boris](https://github.com/BorisKM)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
