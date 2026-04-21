<?php

namespace Gradin\LaravelBenchmarkRoute\Middleware;

use Closure;
use Gradin\LaravelBenchmarkRoute\Attributes\Benchmark;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use ReflectionClass;

class BenchmarkMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $benchmarkStart = microtime(true);
        $benchmarkAttribute = $this->getBenchmarkAttribute($request);

        $response = $next($request);

        if ($benchmarkAttribute && $benchmarkAttribute->enabled) {
            $benchmarkTime = number_format((microtime(true) - $benchmarkStart) * 1000, 2);
            $this->addBenchmarkToResponse($response, $benchmarkTime);
        }

        return $response;
    }

    private function getBenchmarkAttribute(Request $request): ?Benchmark
    {
        try {
            $route = $request->route();
            if (! $route) {
                return null;
            }

            [$controller, $method] = $this->parseControllerAction($route->getActionName());

            $reflection = new ReflectionClass($controller);
            $reflectionMethod = $reflection->getMethod($method);

            $attributes = $reflectionMethod->getAttributes(Benchmark::class);

            return count($attributes) > 0 ? $attributes[0]->newInstance() : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseControllerAction(string $action): array
    {
        if (str_contains($action, '@')) {
            return explode('@', $action);
        }

        [$controller, $method] = explode('::route', str_replace('::class@', '@', $action));

        return [App::make($controller), $method];
    }

    private function addBenchmarkToResponse(Response $response, string $benchmarkTime): void
    {
        if ($response instanceof JsonResource) {
            // If it's a JsonResource, add to additional data
            $response->additional(['benchmark' => ['time' => $benchmarkTime]]);
        } elseif ($response->headers->get('content-type') && str_contains($response->headers->get('content-type'), 'application/json')) {
            // If it's a JSON response, decode, add benchmark, and re-encode
            $data = json_decode($response->getContent(), true);
            $data['benchmark'] = ['time' => $benchmarkTime];
            $response->setContent(json_encode($data));
        }
    }
}
