# gradints/laravel-benchmark-route

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gradints/laravel-benchmark-route.svg?style=flat-square)](https://packagist.org/packages/gradints/laravel-benchmark-route)

This package provides a middleware that adds the elapsed time of a controller action to the response headers. It is designed to be used in Laravel applications and can be easily integrated into your existing routes.

## Installation

You can install the package via Composer:

```bash
composer require --dev gradints/laravel-benchmark-route
```

## Usage

```php
use App\Attributes\Benchmark;
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
    "time": "1.230ms"
  }
}
```

You can also give boolean as an argument to enable or disable the benchmark

```php
#[Benchmark(true)]
#[Benchmark(false)]
```
