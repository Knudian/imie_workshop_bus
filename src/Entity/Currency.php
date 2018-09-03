<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CurrencyRepository")
 * @ORM\Table(name="v_currencies")
 */
class Currency
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", name="currency_code")
     */
    private $id;

    public function getId(): string
    {
        return $this->id;
    }
}
