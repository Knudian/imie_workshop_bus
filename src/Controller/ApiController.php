<?php

namespace App\Controller;

use App\Bean\ClientResponse;
use App\Service\AuthTokenService;
use Lcobucci\JWT\Token;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
     * @Route("/", name="home_page", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction()
    {
        return $this->redirectToRoute('app.swagger_ui');
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
