<?php

namespace Rareloop\Lumberjack\QueryBuilder\Test;

use Mockery;
use PHPUnit\Framework\TestCase;
use Rareloop\Lumberjack\QueryBuilder\Post;
use Rareloop\Lumberjack\QueryBuilder\ScopedQueryBuilder;

class PostQueryBuilderTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /** @test */
    public function can_create_a_builder()
    {
        $builder = Post::createBuilder();

        $this->assertInstanceOf(ScopedQueryBuilder::class, $builder);
        $this->assertArraySubset([
            'post_type' => Post::getPostType(),
        ], $builder->getParameters());
    }

    /** @test */
    public function can_create_a_builder_from_static_functions()
    {
        $this->assertQueryBuilder('whereStatus', ['publish'], TestPost::class);
        $this->assertQueryBuilder('whereIdIn', [[1, 2, 3]], TestPost::class);
        $this->assertQueryBuilder('whereIdNotIn', [[1, 2, 3]], TestPost::class);
    }

    /**
     * @test
     * @expectedException \PHPUnit\Framework\Error\Error
     */
    public function throw_error_on_missing_static_function()
    {
        Post::missingStaticFunction();
    }

    private function assertQueryBuilder($function, $params, $postType)
    {
        $builder = Mockery::mock(ScopedQueryBuilder::class.'['.$function.']', [call_user_func([$postType, 'getPostType'])]);
        $builder->shouldReceive($function)->withArgs($params)->once();

        // Inject the mock builder
        call_user_func([$postType, 'setCreateBuilderResponse'], $builder);

        // Call the static function e.g. $postType::$function($params)
        call_user_func_array([$postType, $function], $params);
    }
}

class TestPost extends Post
{
    private static $injectedBuilder;

    public static function setCreateBuilderResponse($builder)
    {
        static::$injectedBuilder = $builder;
    }

    public static function createBuilder() : ScopedQueryBuilder
    {
        return static::$injectedBuilder;
    }
}
