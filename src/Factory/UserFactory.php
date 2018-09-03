<?php namespace App\Factory;

use App\Constant\MessageCode;
use App\Entity\User;
use App\Exception\User\CredentialsException;
use App\Exception\User\NoModificationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFactory
 * @package App\Factory
 */
class UserFactory extends BaseFactory
{
    /**
     * @var array
     */
    private $sampleUser = array(
        'username'  => null,
        'lastName'  => null,
        'firstName' => null,
        'email'     => null,
        'password'  => 'P@ssword',
    );

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserFactory constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($entityManager);
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param array $input
     * @return User
     */
    public function make(array $input): User
    {
        $data = array_merge($this->sampleUser, $input);
        $user = new User();
        $user->setUsername($data['username'])
            ->setEmail($data['email'])
            ->setLastName($data['lastName'])
            ->setFirstName($data['firstName'])
            ->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));

        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    /**
     * @param User $user
     * @param string $password
     * @throws CredentialsException
     */
    public function canConnect(User $user, string $password): void
    {
        if( !$this->passwordEncoder->isPasswordValid($user, $password)) {
            throw new CredentialsException(MessageCode::INVALID_CREDENTIALS);
        }
    }

    /**
     * @param User $user
     * @param array $data
     * @return User
     * @throws NoModificationException
     */
    public function update(User $user, array $data): User
    {
        $hasBeenModified = false;
        $keys = array_keys($data);
        if (in_array('username', $keys) && $user->getUsername() !== $data['username']) {
            $user->setUsername($data['username']);
            $hasBeenModified = true;
        } else if (in_array('firstName', $keys) && $user->getFirstName() !== $data['firstName']) {
            $user ->setFirstName($data['firstName']);
            $hasBeenModified = true;
        } else if (in_array('lastName', $keys) && $user->getLastName() !== $data['lastName']) {
            $user->setLastName($data['lastName']);
            $hasBeenModified = true;
        }

        if (in_array('password', $keys)) {
            $password = $this->passwordEncoder->encodePassword($user, $data['password']);
            $user->setPassword($password);
            $hasBeenModified = true;
        }

        if ($hasBeenModified) {
            $this->em->persist($user);
            $this->em->flush();
        } else {
            throw new NoModificationException();
        }
        return $user;
    }

    /**
     * @param User $user
     */
    public function delete(User $user) : void
    {
        $user->setUsername('Utilisateur désinscrit')
            ->setFirstName('inconnu')
            ->setLastName('inconnu');

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param User $user
     * @return string
     */
    public function resetPassword(User $user): string
    {
        $newPassword = md5(json_encode($user).time());
        $newPassword = substr($newPassword, rand(0, strlen($newPassword)), 8);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $newPassword));
        $this->em->persist($user);
        $this->em->flush();
        return $newPassword;
    }
}