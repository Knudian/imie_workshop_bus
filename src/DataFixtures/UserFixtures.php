<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @param UserFactory $userFactory
     */
    public function __construct(UserFactory $userFactory)
    {
        $this->userFactory = $userFactory;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = array(
            'username'  => 'test',
            'lastName'  => 'admin',
            'firstName' => 'admin',
            'email'     => 'test@mail.com',
            'password'  => 'test',
        );

        /** @var User $admin */
        $admin = $this->userFactory->make($data);
        $manager->persist($admin);
        $manager->flush();
    }
}
