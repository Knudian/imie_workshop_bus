<?php

namespace App\Bean;

/**
 * Class ClientMessageWarning
 * @package App\Bean
 */
class ClientMessageWarning extends ClientMessage
{
    /**
     * ClientMessageWarning constructor.
     * @param string $code
     * @param array $params
     */
    public function __construct(string $code, array $params = [])
    {
        parent::__construct(self::WARNING, $code, $params);
    }
}