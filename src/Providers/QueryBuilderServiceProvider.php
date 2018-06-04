<?php

namespace Rareloop\Lumberjack\QueryBuilder\Providers;

use Rareloop\Lumberjack\Providers\ServiceProvider;
use Rareloop\Lumberjack\QueryBuilder\Contracts\QueryBuilder as QueryBuilderContract;
use Rareloop\Lumberjack\QueryBuilder\QueryBuilder;

class QueryBuilderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(QueryBuilderContract::class, QueryBuilder::class);
    }
}
