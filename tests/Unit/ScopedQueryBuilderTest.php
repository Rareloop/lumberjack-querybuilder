<?php

namespace Rareloop\Lumberjack\QueryBuilder\Test;

use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;
use Rareloop\Lumberjack\QueryBuilder\Post;
use Rareloop\Lumberjack\QueryBuilder\ScopedQueryBuilder;

class ScopedQueryBuilderTest extends TestCase
{
    /** @test */
    public function correct_post_type_is_set()
    {
        $builder = new ScopedQueryBuilder(Post::class);
        $params = $builder->getParameters();

        $this->assertArraySubset([
            'post_type' => Post::getPostType(),
        ], $params);
    }

    /**
     * @test
     * @expectedException Rareloop\Lumberjack\QueryBuilder\Exceptions\CannotRedeclarePostTypeOnQueryException
     */
    public function cannot_overwrite_post_type()
    {
        $builder = new ScopedQueryBuilder(PostWithQueryScope::class);
        $builder->wherePostType('test_post_type');
    }

    /** @test */
    public function can_call_a_query_scope_on_post_object()
    {
        $builder = new ScopedQueryBuilder(PostWithQueryScope::class);
        $chainedBuilder = $builder->inDraft();
        $params = $builder->getParameters();

        $this->assertSame($builder, $chainedBuilder);
        $this->assertArraySubset([
            'post_status' => 'draft',
        ], $params);
    }

    /**
     * @test
     * @expectedException \PHPUnit\Framework\Error\Error
     */
    public function missing_query_scope_throws_an_error()
    {
        $builder = new ScopedQueryBuilder(PostWithQueryScope::class);
        $builder->nonExistentScope();
    }
}

class PostWithQueryScope extends Post
{
    public function scopeInDraft($query)
    {
        return $query->whereStatus('draft');
    }
}
