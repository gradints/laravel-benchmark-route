<?php

namespace Gradin\LaravelBenchmarkRoute\Attributes;

use Attribute;

#[Attribute]
class Benchmark
{
    public function __construct(public bool $enabled = true) {}
}
