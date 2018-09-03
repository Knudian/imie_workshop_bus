<?php

namespace App\Factory;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class BaseFactory
 * @package App\Factory
 */
abstract class BaseFactory
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * AnswerFactory constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }
}