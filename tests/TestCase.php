<?php

namespace AugmentMy\LaravelQueryPagination\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use AugmentMy\LaravelQueryPagination\LaravelQueryPaginationServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelQueryPaginationServiceProvider::class,
        ];
    }
}
