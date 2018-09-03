<?php

namespace App\Bean;

use App\Constant\MessageCode;
use JsonSerializable;

/**
 * Class ClientMessage
 * @package App\Bean
 */
class ClientMessage implements JsonSerializable
{
    const STANDARD = 'standard';
    const INFO = 'info';
    const WARNING = 'warning';
    const ERROR = 'error';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $code;

    /**
     * @var array
     */
    private $params;

    /**
     * ClientMessage constructor.
     * @param string $type
     * @param string $code
     * @param array $params
     */
    public function __construct(string $type, string $code, array $params = [])
    {
        $this->type = $type;
        $this->code = $code;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function jsonSerialize() :array
    {
        $response = array();
        if ($this->code !== MessageCode::NONE)
            $response['code'] = $this->code;
        if (count($this->params) > 0)
            $response['params'] = $this->params;
        return $response;
    }
}