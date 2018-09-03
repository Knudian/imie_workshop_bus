<?php

namespace App\Constant;

/**
 * Class HttpCode
 * @package App\Constant
 */
final class HttpCode extends AbstractConstant
{
    const OK                = 200;
    const CREATED           = 201;
    const ACCEPTED          = 202;
    const NO_CONTENT        = 204;
    const RESET             = 205;

    const NOTHING_CHANGED   = 304;

    const BAD_REQUEST       = 400;
    const UNAUTHORIZED      = 401;
    const FORBIDDEN         = 403;
    const NOT_FOUND         = 404;
    const NOT_ALLOWED       = 405;
}