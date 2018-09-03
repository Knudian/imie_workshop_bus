<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\InvalidTokenException;
use App\Repository\UserRepository;
use Lcobucci\JWT\{Builder, Parser, Token, ValidationData};
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AuthTokenService
 * @package App\Service
 */
class AuthTokenService
{
    /**
     * @var string
     */
    private $issuer;

    /**
     * @var string
     */
    private $audience;

    /**
     * @var Sha256
     */
    private $signer;

    /**
     * @var string
     */
    private $signature;

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var int
     */
    private $notBeforeDelay;

    /**
     * @var int
     */
    private $ttl;

    /**
     * AuthTokenService constructor.
     * @param ContainerInterface $container
     * @param UserRepository $userRepository
     */
    public function __construct(ContainerInterface $container, UserRepository $userRepository)
    {
        $this->issuer           = $container->getParameter('jwt.issuer');
        $this->audience         = $container->getParameter('jwt.audience');
        $this->signature        = $container->getParameter('jwt.signature');
        $this->signer           = new Sha256();
        $this->repository       = $userRepository;
        $this->notBeforeDelay   = $container->getParameter('jwt.delay.notbefore');
        $this->ttl              = $container->getParameter('jwt.delay.ttl');
    }

    /**
     * @param User $user
     * @return Token
     */
    public function createToken(User $user): Token
    {
        $builder = new Builder();
        $builder->setIssuer($this->issuer)
            ->setAudience($this->audience)
            ->setId('api', true)
            ->setSubject($user->serialize())
            ->setIssuedAt(time())
            ->setNotBefore(time() + 0)
            ->setExpiration(time() +3600)
            ->sign($this->signer, $this->signature);

        return $builder->getToken();
    }

    /**
     * @param string $tokenAsString
     * @return Token
     */
    public function parseToken(string $tokenAsString): Token
    {
        $parser = new Parser();
        return $parser->parse($tokenAsString);
    }

    /**
     * @param Token $token
     * @throws InvalidTokenException
     */
    public function validateToken(Token $token): void
    {
        $validationData = new ValidationData();
        $validationData->setIssuer($this->issuer);
        $validationData->setAudience($this->issuer);
        $validationData->setId('api');
        if (!$token->validate($validationData)) {
            throw new InvalidTokenException();
        }
    }

    /**
     * @param Token $token
     * @return bool
     */
    public function verifySignature(Token $token): bool
    {
        return $token->verify($this->signer, $this->signature);
    }

    /**
     * @param Token $token
     * @return null|User
     */
    public function getUserFromToken(Token $token): User
    {
        $user = new User();
        $user->unserialize($token->getClaim('sub'));
        return $user;
    }
}