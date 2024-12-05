<?php

namespace EntrieCloud\LaravelCockpit\Http\Controllers;

use EntrieCloud\LaravelCockpit\Facades\LaravelCockpit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

class LaravelCockpitClearController extends Controller
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function __invoke(Request $request, string $secrets): Response|RedirectResponse
    {
        $secretsArray = explode(',', $secrets);
        $secretsArray = array_filter($secretsArray);

        $connections = collect(config('cockpit.connections'))
            ->filter(fn ($config, $key) => in_array(Arr::get($config, 'cache_clear_secret'), $secretsArray, true))
            ->keys();

        $connections
            ->each(fn ($connection) => LaravelCockpit::connection($connection)
                ->flushCache());

        if ($request->isJson()) {
            return response()->noContent();
        }

        return back();
    }
}
