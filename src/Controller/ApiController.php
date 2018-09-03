<?php

namespace App\Controller;

use App\Bean\ClientResponse;
use App\Service\AuthTokenService;
use Lcobucci\JWT\Token;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiController
 * @package App\Controller
 */
class ApiController extends Controller
{
    /**
     * @var AuthTokenService
     */
    private $authTokenService;

    /**
     * ApiController constructor.
     * @param AuthTokenService $authTokenService
     */
    public function __construct(AuthTokenService $authTokenService)
    {
        $this->authTokenService = $authTokenService;
    }

    /**
     * @param Request $request
     * @return Token
     */
    protected function parseToken(Request $request): Token
    {
        $token = $request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $token);
        return $this->authTokenService->parseToken($token);
    }

    /**
     * @param ClientResponse $response
     * @return JsonResponse
     */
    protected function buildResponse(ClientResponse $response): JsonResponse
    {
        return new JsonResponse(
            $response,
            $response->getHttpCode(),
            []
        );
    }
}
