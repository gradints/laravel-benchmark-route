# gradints/laravel-benchmark-route

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gradints/laravel-benchmark-route.svg?style=flat-square)](https://packagist.org/packages/gradints/laravel-benchmark-route)

This package provides a middleware that adds the elapsed time of a controller action to the response headers. It is designed to be used in Laravel applications and can be easily integrated into your existing routes.

## Installation

You can install the package via Composer:

```bash
composer require --dev gradints/laravel-benchmark-route
```

After installing it, you need to register the middleware in your bootstrap/app.php file:

```php
use Gradin\LaravelBenchmarkRoute\Middleware\BenchmarkMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // register BenchmarkMiddleware for web and api routes
        $middleware->web(BenchmarkMiddleware::class);
        $middleware->api(BenchmarkMiddleware::class);
    });
```

## Usage

```php
use Gradin\LaravelBenchmarkRoute\Attributes\Benchmark;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductController extends Controller
{
    #[Benchmark]
    public function index()
    {
        $products = Product::paginate(20);

        return JsonResource::collection($products);
    }
}
```

And the response will be like this:

```json
{
  "data": [
    // products
  ],
  "meta": {
    // pagination meta data
  },
  "benchmark": {
    "time": "1,230.93"
  }
}
```

You can also give boolean as an argument to enable or disable the benchmark

```php
#[Benchmark(true)]
#[Benchmark(false)]
```
