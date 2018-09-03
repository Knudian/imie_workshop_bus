<?php

namespace App\Constant;

/**
 * Class Role
 * @package App\Constant
 */
final class Role extends AbstractConstant
{
    const ROLE_USER     = 'ROLE_USER';
    const ROLE_ADMIN    = 'ROLE_ADMIN';

    public static function getList(): array
    {
        return (new Role)->getConstants();
    }
}