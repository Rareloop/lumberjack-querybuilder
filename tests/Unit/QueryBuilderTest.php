<?php

namespace Rareloop\Lumberjack\QueryBuilder\Test;

use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;
use Rareloop\Lumberjack\QueryBuilder\QueryBuilder;
use Timber\Timber;

class QueryBuilderTest extends TestCase
{
    /** @test */
    public function correct_post_type_is_set()
    {
        $builder = new QueryBuilder();
        $chainedBuilder = $builder->wherePostType('test_post_type');
        $params = $builder->getParameters();

        $this->assertArraySubset([
            'post_type' => 'test_post_type',
        ], $params);
    }

    /** @test */
    public function can_limit_post_count()
    {
        $builder = new QueryBuilder();
        $chainedBuilder = $builder->limit(10);
        $params = $builder->getParameters();

        $this->assertSame($builder, $chainedBuilder);
        $this->assertArraySubset([
            'posts_per_page' => 10,
        ], $params);
    }

    /** @test */
    public function can_set_post_offset()
    {
        $builder = new QueryBuilder();
        $chainedBuilder = $builder->offset(10);
        $params = $builder->getParameters();

        $this->assertSame($builder, $chainedBuilder);
        $this->assertArraySubset([
            'offset' => 10,
        ], $params);
    }

    /** @test */
    public function can_set_order()
    {
        $builder = new QueryBuilder();
        $chainedBuilder = $builder->orderBy('menu_order', 'DESC');
        $params = $builder->getParameters();

        $this->assertSame($builder, $chainedBuilder);
        $this->assertArraySubset([
            'orderby' => 'menu_order',
            'order' => 'DESC',
        ], $params);
    }

    /** @test */
    public function can_set_order_by_meta()
    {
        $builder = new QueryBuilder();
        $chainedBuilder = $builder->orderByMeta('test_meta_key', 'DESC');
        $params = $builder->getParameters();

        $this->assertSame($builder, $chainedBuilder);
        $this->assertArraySubset([
            'orderby' => 'meta_value',
            'meta_key' => 'test_meta_key',
            'order' => 'DESC',
        ], $params);
    }

    /** @test */
    public function can_set_order_by_meta_with_numeric_ordering()
    {
        $builder = new QueryBuilder();
        $chainedBuilder = $builder->orderByMeta('test_meta_key', 'DESC', true);
        $params = $builder->getParameters();

        $this->assertSame($builder, $chainedBuilder);
        $this->assertArraySubset([
            'orderby' => 'meta_value_num',
            'meta_key' => 'test_meta_key',
            'order' => 'DESC',
        ], $params);
    }

    /** @test */
    public function can_restrict_to_ids_in_array()
    {
        $builder = new QueryBuilder();
        $chainedBuilder = $builder->whereIdIn([1, 2, 3]);
        $params = $builder->getParameters();

        $this->assertSame($builder, $chainedBuilder);
        $this->assertArraySubset([
            'post__in' => [1, 2, 3],
        ], $params);
    }

    /** @test */
    public function can_filter_by_status()
    {
        $builder = new QueryBuilder();
        $chainedBuilder = $builder->whereStatus('publish');
        $params = $builder->getParameters();

        $this->assertSame($builder, $chainedBuilder);
        $this->assertArraySubset([
            'post_status' => 'publish',
        ], $params);
    }

    /** @test */
    public function can_add_a_single_meta_query()
    {
        $builder = new QueryBuilder();
        $chainedBuilder = $builder->whereMeta('test_meta_key', 'test_meta_value');
        $params = $builder->getParameters();

        $this->assertSame($builder, $chainedBuilder);
        $this->assertArraySubset([
            'meta_query' => [
                [
                    'key' => 'test_meta_key',
                    'value' => 'test_meta_value',
                    'compare' => '=',
                ]
            ],
        ], $params);
    }

    /** @test */
    public function can_add_multiple_meta_queries()
    {
        $builder = new QueryBuilder();
        $builder->whereMeta('test_meta_key1', 'test_meta_value1');
        $builder->whereMeta('test_meta_key2', 'test_meta_value2');
        $params = $builder->getParameters();

        $this->assertArraySubset([
            'meta_query' => [
                [
                    'key' => 'test_meta_key1',
                    'value' => 'test_meta_value1',
                    'compare' => '=',
                ],
                [
                    'key' => 'test_meta_key2',
                    'value' => 'test_meta_value2',
                    'compare' => '=',
                ]
            ],
        ], $params);
    }

    /** @test */
    public function can_set_comparison_operator_on_meta_query()
    {
        $builder = new QueryBuilder();
        $chainedBuilder = $builder->whereMeta('test_meta_key', 'test_meta_value', '>=');
        $params = $builder->getParameters();

        $this->assertSame($builder, $chainedBuilder);
        $this->assertArraySubset([
            'meta_query' => [
                [
                    'key' => 'test_meta_key',
                    'value' => 'test_meta_value',
                    'compare' => '>=',
                ]
            ],
        ], $params);
    }

    /** @test */
    public function can_set_type_on_meta_query()
    {
        $builder = new QueryBuilder();
        $chainedBuilder = $builder->whereMeta('test_meta_key', 'test_meta_value', '>=', 'NUMERIC');
        $params = $builder->getParameters();

        $this->assertSame($builder, $chainedBuilder);
        $this->assertArraySubset([
            'meta_query' => [
                [
                    'key' => 'test_meta_key',
                    'value' => 'test_meta_value',
                    'compare' => '>=',
                    'type' => 'NUMERIC',
                ]
            ],
        ], $params);
    }

    /** @test */
    public function can_set_meta_query_relation()
    {
        $builder = new QueryBuilder();
        $chainedBuilder = $builder->whereMetaRelationshipIs('OR');
        $builder->whereMeta('test_meta_key1', 'test_meta_value1');
        $builder->whereMeta('test_meta_key2', 'test_meta_value2');
        $params = $builder->getParameters();

        $this->assertSame($builder, $chainedBuilder);
        $this->assertArraySubset([
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => 'test_meta_key1',
                    'value' => 'test_meta_value1',
                    'compare' => '=',
                ],
                [
                    'key' => 'test_meta_key2',
                    'value' => 'test_meta_value2',
                    'compare' => '=',
                ]
            ],
        ], $params);
    }

    /**
     * @test
     * @expectedException     Rareloop\Lumberjack\QueryBuilder\Exceptions\InvalidMetaRelationshipException
     */
    public function invalid_meta_realtionship_throws_an_exception()
    {
        $builder = new QueryBuilder();
        $builder->whereMetaRelationshipIs('INVALID');
    }
}
