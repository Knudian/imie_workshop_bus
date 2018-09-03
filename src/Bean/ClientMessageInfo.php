<?php

namespace App\Bean;

/**
 * Class ClientMessageInfo
 * @package App\Bean
 */
class ClientMessageInfo extends ClientMessage
{
    /**
     * ClientMessageInfo constructor.
     * @param string $code
     * @param array $params
     */
    public function __construct(string $code, array $params = [])
    {
        parent::__construct(self::INFO, $code, $params);
    }
}