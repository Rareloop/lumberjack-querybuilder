<?php

namespace Rareloop\Lumberjack\QueryBuilder\Test\Providers;

use PHPUnit\Framework\TestCase;
use Rareloop\Lumberjack\Application;
use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Http\Lumberjack;
use Rareloop\Lumberjack\QueryBuilder\Contracts\QueryBuilder as QueryBuilderContract;
use Rareloop\Lumberjack\QueryBuilder\QueryBuilderServiceProvider;

class PostQueryBuilderTest extends TestCase
{
    /** @test */
    public function query_builder_is_registered_into_container()
    {
        $app = new Application;
        $provider = new QueryBuilderServiceProvider($app);

        $provider->register();

        $this->assertTrue($app->has(QueryBuilderContract::class));
    }
}
