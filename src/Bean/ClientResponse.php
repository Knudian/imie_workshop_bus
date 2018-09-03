<?php

namespace App\Bean;

use DateTime;
use JsonSerializable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ClientResponse
 * @package App\Bean
 */
class ClientResponse implements JsonSerializable
{
    /**
     * @var DateTime
     */
    private $date;
    /**
     * @var ArrayCollection|ClientMessage[]
     */
    private $messageBag;

    /**
     * @var mixed
     */
    private $response;

    /**
     * @var int
     */
    private $httpCode = 200;

    /**
     * ClientResponse constructor.
     */
    public function __construct()
    {
        $this->messageBag = new ArrayCollection();
        $this->date = new DateTime('now');
        $this->response = [];
    }

    /**
     * @param int $code
     * @return ClientResponse
     */
    public function setHttpCode(int $code): self
    {
        $this->httpCode = $code;
        return $this;
    }

    /**
     * @param ClientMessage $message
     * @return ClientResponse
     */
    public function addMessage(ClientMessage $message): self
    {
        $this->messageBag->add($message);
        return $this;
    }

    /**
     * @return ClientMessage[]|ArrayCollection
     */
    public function getMessageList(): ArrayCollection
    {
        return $this->messageBag;
    }

    /**
     * @param string $type
     * @return ArrayCollection
     */
    private function getMessageOfType(string $type): ArrayCollection
    {
        $messageList = new ArrayCollection();
        if (in_array($type, [ClientMessage::INFO, ClientMessage::ERROR, ClientMessage::WARNING])) {
            foreach ($this->messageBag as $message)
            {
                if ($message->getType() === $type) {
                    $messageList->add($message);
                }
            }
        }
        return $messageList;
    }

    /**
     * @return ArrayCollection
     */
    public function infos(): ArrayCollection
    {
        return $this->getMessageOfType(ClientMessage::INFO);
    }

    /**
     * @return ArrayCollection
     */
    public function warnings(): ArrayCollection
    {
        return $this->getMessageOfType(ClientMessage::WARNING);
    }

    /**
     * @return ArrayCollection
     */
    public function errors(): ArrayCollection
    {
        return $this->getMessageOfType(ClientMessage::ERROR);
    }

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     * @param ClientMessage $response
     * @return ClientResponse
     */
    public function setResponse(ClientMessage $response): self
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function jsonSerialize()
    {
        $response = array(
            'date' => $this->date->getTimestamp()
        );
        if ($this->errors()->count() > 0) {
            $response['errors'] = $this->errors()->toArray();
        } else if ($this->warnings()->count() > 0 ) {
            $response['warnings'] = $this->warnings()->toArray();
        } else if ($this->infos()->count() > 0) {
            $response['infos'] = $this->infos()->toArray();
        } else {
            $response['response'] = $this->getResponse();
        }
        return $response;
    }

    public function __toString()
    {
        return json_encode($this->jsonSerialize());
    }

}