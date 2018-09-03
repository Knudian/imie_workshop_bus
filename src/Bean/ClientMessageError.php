<?php

namespace App\Bean;

/**
 * Class ClientMessageError
 * @package App\Bean
 */
class ClientMessageError extends ClientMessage
{
    /**
     * ClientMessageError constructor.
     * @param string $code
     * @param array $params
     */
    public function __construct(string $code, array $params = [])
    {
        parent::__construct(self::ERROR, $code, $params);
    }
}