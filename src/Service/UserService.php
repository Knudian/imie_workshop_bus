<?php

namespace App\Service;

use App\Bean\{ClientMessage, ClientMessageError, ClientMessageInfo, ClientResponse};
use App\Constant\{HttpCode, MessageCode};
use App\Exception\AuthorizationException;
use App\Exception\InvalidTokenException;
use App\Exception\User\CredentialsException;
use App\Exception\User\NoModificationException;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Lcobucci\JWT\Token;

/**
 * Class UserService
 * @package App\Service
 */
class UserService extends BaseService
{
    /** @var UserFactory */
    private $userFactory;

    /** @var UserRepository */
    private $userRepository;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     * @param AuthTokenService $authTokenService
     * @param UserFactory $userFactory
     */
    public function __construct(
        UserRepository $userRepository,
        AuthTokenService $authTokenService,
        UserFactory $userFactory
    ) {
        parent::__construct($authTokenService);
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Token $token
     * @param array $inputs
     * @return ClientResponse
     */
    public function create(Token $token, array $inputs): ClientResponse
    {
        $clientResponse = new ClientResponse();
        try {
            $this->authTokenService->validateToken($token);
            $maker = $this->authTokenService->getUserFromToken($token);
            $this->userCanModify($maker);
            $user = $this->userFactory->make($inputs);
            $clientResponse->setHttpCode(Httpcode::CREATED)
                ->setResponse(
                    new ClientMessage(
                        ClientMessage::STANDARD,
                        MessageCode::USER_CREATED,
                        $user->jsonSerialize()
                    )
                );
        } catch (AuthorizationException $e) {
            $clientResponse
                ->setHttpCode(HttpCode::UNAUTHORIZED)
                ->addMessage(
                    new ClientMessageError(MessageCode::INSUFFICIENT_RIGHTS)
                );
        } catch (InvalidTokenException $e) {
            $clientResponse
                ->setHttpCode(HttpCode::FORBIDDEN)
                ->addMessage(
                    new ClientMessageError(MessageCode::TOKEN_EXPIRED)
                );
        }
        return $clientResponse;
    }

    /**
     * @param Token $token
     * @param int $userId
     * @param array $inputs
     * @return ClientResponse
     */
    public function update(Token $token, int $userId, array $inputs): ClientResponse
    {
        $clientResponse = new ClientResponse();
        try {
            $this->authTokenService->validateToken($token);
            $maker = $this->authTokenService->getUserFromToken($token);
            if (!$maker->getId() !== $userId) {
                $this->userCanModify($maker);
            }
            $user = $this->userRepository->find($userId);
            $maker = $this->userFactory->update($user, $inputs);
            $clientResponse
                ->setHttpCode(Httpcode::ACCEPTED)
                ->setResponse(
                    new ClientMessage(
                        ClientMessage::STANDARD,
                        MessageCode::USER_UPDATED,
                        $maker->jsonSerialize()
                    )
                );
        } catch (NoModificationException $e) {
            $clientResponse
                ->setHttpCode(HttpCode::NOTHING_CHANGED)
                ->addMessage(
                    new ClientMessageInfo(MessageCode::NOTHING_CHANGED)
                );
        } catch (AuthorizationException $exception) {
            $clientResponse
                ->setHttpCode(HttpCode::UNAUTHORIZED)
                ->addMessage(new ClientMessageError(MessageCode::INSUFFICIENT_RIGHTS));
        } catch (InvalidTokenException $e) {
            $clientResponse
                ->setHttpCode(HttpCode::FORBIDDEN)
                ->addMessage(
                    new ClientMessageError(MessageCode::TOKEN_EXPIRED)
                );
        }
        return $clientResponse;
    }

    /**
     * @param Token $token
     * @param int $userId
     * @return ClientResponse
     */
    public function delete(Token $token, int $userId): ClientResponse
    {
        $clientResponse = new ClientResponse();
        try {
            $this->authTokenService->validateToken($token);
            $maker = $this->authTokenService->getUserFromToken($token);
            if (!$maker->getId() !== $userId)
                $this->userCanModify($maker);
            $user = $this->userRepository->find($userId);
            $this->userFactory->delete($user);
            $clientResponse
                ->setHttpCode(HttpCode::ACCEPTED);
        } catch (AuthorizationException $e) {
            $clientResponse
                ->setHttpCode(HttpCode::UNAUTHORIZED)
                ->addMessage(new ClientMessageError(MessageCode::INSUFFICIENT_RIGHTS));
        } catch (InvalidTokenException $e) {
            $clientResponse
                ->setHttpCode(HttpCode::FORBIDDEN)
                ->addMessage(
                    new ClientMessageError(MessageCode::TOKEN_EXPIRED)
                );
        }
        return $clientResponse;
    }

    /**
     * @param string $login
     * @param string $password
     * @return ClientResponse
     */
    public function connect(string $login, string $password): ClientResponse
    {
        $clientResponse = new ClientResponse();

        try {
            $user = $this->userRepository->findOneBy(['username' => $login]);
            if (is_null($user)) {
                throw new CredentialsException();
            }
            $this->userFactory->canConnect($user, $password);
            $token = $this->authTokenService->createToken($user);
            $clientResponse
                ->setHttpCode(HttpCode::OK)
                ->setResponse(
                    new ClientMessage(
                        ClientMessage::STANDARD,
                        MessageCode::NEW_TOKEN,
                        array(
                            'token' => $token->__toString(),
                            'user'  => $user->jsonSerialize()
                        )
                    )
                );
        } catch (CredentialsException $e) {
            $clientResponse
                ->setHttpCode(HttpCode::UNAUTHORIZED)
                ->addMessage(
                    new ClientMessageError(MessageCode::INVALID_CREDENTIALS, ['login' => $login, 'password' => $password])
                );
        }
        return $clientResponse;
    }

    /**
     * @param string $email
     * @return ClientResponse
     */
    public function resetPassword(string $email): ClientResponse
    {
        $clientResponse = new ClientResponse();
        try {
            $user = $this->userRepository->findOneBy(['email' => $email]);
            if (is_null($user))
                throw new EntityNotFoundException();
            $newPassword = $this->userFactory->resetPassword($user);
            $clientResponse
                ->setHttpCode(HttpCode::OK)
                ->addMessage(
                    new ClientMessageInfo(
                        MessageCode::USER_PASSWORD_RESET,
                        ['pwd'  => $newPassword]
                    )
                );
        } catch (EntityNotFoundException $e) {
            $clientResponse
                ->setHttpCode(HttpCode::BAD_REQUEST)
                ->addMessage(
                    new ClientMessageError(MessageCode::USER_NOT_FOUND)
                );
        }
        return $clientResponse;
    }

    /**
     * @param Token $token
     * @param int $userId
     * @return ClientResponse
     */
    public function getProfile(Token $token, int $userId): ClientResponse
    {
        $clientResponse = new ClientResponse();
        try {
            $this->authTokenService->validateToken($token);
            $user = $this->userRepository->find($userId);
            if (is_null($user))
                throw new EntityNotFoundException();
            $clientResponse
                ->setHttpCode(HttpCode::OK)
                ->addMessage(
                    new ClientMessage(
                        ClientMessage::STANDARD,
                        MessageCode::NONE,
                        $user->jsonSerialize()
                    )
                );
        } catch (EntityNotFoundException $e) {
            $clientResponse
                ->setHttpCode(HttpCode::NOT_FOUND)
                ->addMessage(
                    new ClientMessageError(MessageCode::USER_NOT_FOUND)
                );
        } catch (InvalidTokenException $e) {
            $clientResponse
                ->setHttpCode(HttpCode::FORBIDDEN)
                ->addMessage(
                    new ClientMessageError(MessageCode::TOKEN_EXPIRED)
                );
        }
        return $clientResponse;
    }
}