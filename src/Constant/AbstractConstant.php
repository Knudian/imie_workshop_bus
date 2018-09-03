<?php

namespace App\Constant;

use ReflectionClass;

/**
 * Class AbstractConstant
 * @package App\Constant
 */
abstract class AbstractConstant
{
    /**
     * @return array
     */
    protected function getConstants()
    {
        try {
            $reflectionClass = new ReflectionClass($this);
            return $reflectionClass->getConstants();
        } catch (\ReflectionException $e) {
            return [];
        }
    }
}