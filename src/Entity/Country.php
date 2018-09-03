<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 */
class Country implements JsonSerializable
{
    /**
     * @var string
     * @ORM\Column(type="string", length=2)
     */
    private $countryCode;

    /**
     * @var string
     * @ORM\Column(type="string", length=3)
     */
    private $currencyCode;

    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string", length=3)
     */
    private $isoAlpha3;

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @return string
     */
    public function getIsoAlpha3(): string
    {
        return $this->isoAlpha3;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): array
    {
        return array(
            'isoAlpha3'     => $this->isoAlpha3,
            'countryCode'   => $this->countryCode,
            'currencyCode'  => $this->currencyCode,
        );
    }
}
