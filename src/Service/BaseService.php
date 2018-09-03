<?php

namespace App\Service;

use App\Constant\Role;
use App\Entity\User;
use App\Exception\AuthorizationException;

/**
 * Class BaseService
 * @package App\Service
 */
abstract class BaseService
{
    /**
     * @var AuthTokenService
     */
    protected $authTokenService;

    /**
     * BaseService constructor.
     * @param AuthTokenService $authTokenService
     */
    public function __construct(AuthTokenService $authTokenService)
    {
        $this->authTokenService = $authTokenService;
    }

    /**
     * @param User $user
     * @throws AuthorizationException
     */
    protected function userCanModify(User $user): void
    {
        if (!$user->hasRole(Role::ROLE_ADMIN)) {
            throw new AuthorizationException();
        }
    }
}