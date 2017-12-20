<?php

namespace Rareloop\Lumberjack\QueryBuilder;

use Rareloop\Lumberjack\Post as LumberjackPost;
use Rareloop\Lumberjack\QueryBuilder\Exceptions\PostTypeRegistrationException;
use Rareloop\Lumberjack\QueryBuilder\ScopedQueryBuilder;

class Post extends LumberjackPost
{
    public function __construct($pid = false, $preventTimberConstructor = false)
    {
        if (!$preventTimberConstructor) {
            parent::__construct($pid);
        }
    }
    
    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, ['whereStatus', 'whereIdIn', 'whereIdNotIn'])) {
            $builder = static::createBuilder();
            return call_user_func_array([$builder, $name], $arguments);
        }

        trigger_error('Call to undefined method '.__CLASS__.'::'.$name.'()', E_USER_ERROR);
    }

    public static function createBuilder() : ScopedQueryBuilder
    {
        return new ScopedQueryBuilder(static::class);
    }
}
