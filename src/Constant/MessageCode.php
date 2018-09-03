<?php

namespace App\Constant;

/**
 * Class MessageCode
 * @package App\Constant
 */
final class MessageCode extends AbstractConstant
{
    const NONE = 'NONE';

    const INVALID_CREDENTIALS = 'INVALID_CREDENTIALS';
    const INSUFFICIENT_RIGHTS = 'INSUFFICIENT_RIGHTS';

    const CANNOT_DEACTIVATE_YOURSELF = 'CANNOT_DEACTIVATE_YOURSELF';

    const NOTHING_CHANGED       = 'NOTHING_CHANGED';

    const USER_CREATED = 'CREATED';
    const USER_UPDATED = 'UPDATED';
    const USER_NOT_FOUND = 'USER_NOT_FOUND';
    const USER_PASSWORD_RESET = 'USER_PASSWORD_RESET';

    const NEW_TOKEN = 'NEW_TOKEN';
    const TOKEN_EXPIRED = 'TOKEN_EXPIRED';
}