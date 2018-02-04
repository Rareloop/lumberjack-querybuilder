<?php

namespace Rareloop\Lumberjack\QueryBuilder;

use Rareloop\Lumberjack\QueryBuilder\Exceptions\CannotRedeclarePostTypeOnQueryException;
use Rareloop\Lumberjack\QueryBuilder\QueryBuilder;

class ScopedQueryBuilder extends QueryBuilder
{
    public function __construct($postClass)
    {
        $this->postClass = $postClass;
    }

    public function __call($name, $arguments)
    {
        $scopeFunctionName = 'scope' . ucfirst($name);

        $post = new $this->postClass(false, true);

        if (!method_exists($post, $scopeFunctionName)) {
            trigger_error('Call to undefined method '.$this->postClass.'::'.$scopeFunctionName.'()', E_USER_ERROR);
        }

        return $post->{$scopeFunctionName}($this);
    }

    public function getParameters()
    {
        return array_merge(parent::getParameters(), ['post_type' => call_user_func([$this->postClass, 'getPostType'])]);
    }

    public function wherePostType($postType)
    {
        throw new CannotRedeclarePostTypeOnQueryException;
    }
    
    public function get()
    {
        return $this->postClass::query($this->getParameters());
    }
}
