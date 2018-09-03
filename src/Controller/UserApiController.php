<?php

namespace App\Controller;

use App\Service\AuthTokenService;
use App\Service\UserService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\{ JsonResponse, Request };
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserApiController
 * @package App\Controller
 */
class UserApiController extends ApiController
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserApiController constructor.
     * @param AuthTokenService $authTokenService
     * @param UserService $service
     */
    public function __construct(AuthTokenService $authTokenService, UserService $service)
    {
        parent::__construct($authTokenService);
        $this->userService = $service;
    }

    /**
     * Sends back a JWT Token
     * @Route("/api/login", name="user_login", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns a JWT token for the given user"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Invalid credentials"
     * )
     * @SWG\Parameter(
     *     name="login",
     *     in="query",
     *     type="string",
     *     description="The user's login, its username."
     * )
     * @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     type="string",
     *     description="The user's password"
     * )
     * @SWG\Tag(name="User Management routes")
     * @param Request $request
     * @return JsonResponse
     */
    public function loginAction(Request $request): JsonResponse
    {
        $login      = $request->get('login');
        $password   = $request->get('password');

        $clientResponse = $this->userService->connect($login, $password);

        return new JsonResponse(
            $clientResponse, $clientResponse->getHttpCode(), []
        );
    }

    /**
     * Reset the user with the given email password.
     * @Route("/api/forgotten_password", name="user_forgottenpassword", methods={"POST"})
     * @SWG\Parameter(
     *     name="email",
     *     in="query",
     *     type="string",
     *     description="The User's registered email to reset"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="An email containing the new password has been sent"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="No user matches the given email"
     * )
     * @SWG\Tag(name="User Management routes")
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPasswordAction(Request $request): JsonResponse
    {
        $email      = $request->get('email');
        $response = $this->userService->resetPassword($email);
        return $this->buildResponse($response);
    }

    /**
     * Updates a user profile
     * @Route("/api/user/{userId}", name="user_update", methods={"PATCH"})
     *
     * @SWG\Parameter(
     *     name="userId",
     *     type="integer",
     *     in="path",
     *     description="A user unique identifier"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="The updated profile of the given user",
     *     @SWG\Schema(
     *         ref=@Model(type=App\Entity\User::class)
     *     )
     * )
     * @SWG\Response(
     *     response=304,
     *     description="Nothing has changed, request was not applied"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid request"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Insufficient rights"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Token has expired"
     * )
     * @Security(name="Bearer")
     * @SWG\Tag(name="User Management routes")
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function updateAction(Request $request, int $userId): JsonResponse
    {
        $token = $this->parseToken($request);
        $data = array(
            'username' => $request->get('username', ''),
            'firstName'=> $request->get('firstName', ''),
            'lastName' => $request->get('lastName', '')
        );
        $response = $this->userService->update($token, $userId, $data);
        return $this->buildResponse($response);
    }

    /**
     * Deletes a user account
     * @Route("/api/user/{userId}", name="user_delete", methods={"DELETE"})
     *
     * @SWG\Parameter(
     *     name="userId",
     *     type="integer",
     *     in="path",
     *     description="A user unique identifier"
     * )
     * @SWG\Response(
     *     response=202,
     *     description="The updated profile of the given user"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Insufficient rights"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Token has expired"
     * )
     * @Security(name="Bearer")
     * @SWG\Tag(name="User Management routes")
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function deleteAction(Request $request, int $userId): JsonResponse
    {
        $token = $this->parseToken($request);
        $response = $this->userService->delete($token, $userId);
        return $this->buildResponse($response);
    }

    /**
     * Get a user profile
     * @Route("/api/user/{userId}", name="user_profile", methods={"GET"})
     *
     * @SWG\Parameter(
     *     name="userId",
     *     type="integer",
     *     in="path",
     *     description="A user unique identifier"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="The updated profile of the given user",
     *     @SWG\Schema(
     *         ref=@Model(type=App\Entity\User::class)
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="User not found"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Token has expired"
     * )
     * @Security(name="Bearer")
     * @SWG\Tag(name="User Management routes")
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function profileAction(Request $request, int $userId): JsonResponse
    {
        $token = $this->parseToken($request);
        $response = $this->userService->getProfile($token, $userId);
        return $this->buildResponse($response);
    }

    /**
     * Creates a new User, and send an email to him.
     * @Route("/api/user", name="user_creation", methods={"POST"})
     * @SWG\Parameter(
     *     name="username",
     *     in="formData",
     *     type="string",
     *     description="The username of the new User. Must be unique"
     * )
     * @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     type="string",
     *     description="The email of the new User. Must be unique"
     * )
     * @SWG\Parameter(
     *     name="last_name",
     *     in="formData",
     *     type="string",
     *     description="The last name of the new User"
     * )
     * @SWG\Parameter(
     *     name="first_name",
     *     in="formData",
     *     type="string",
     *     description="The first name of the new User"
     * )
     * @SWG\Parameter(
     *     name="licence",
     *     in="formData",
     *     type="string",
     *     description="The licence reference of the new User"
     * )
     * @SWG\Response(
     *     response=201,
     *     description="The user has been created",
     *     @SWG\Schema(
     *         ref=@Model(type=App\Entity\User::class)
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Token has expired"
     * )
     * @Security(name="Bearer")
     * @SWG\Tag(name="User Management routes")
     * @param Request $request
     * @return JsonResponse
     */
    public function createUserAction(Request $request): JsonResponse
    {
        $token = $this->parseToken($request);
        $inputs = array(
            'username'  => $request->get('username'),
            'email'     => $request->get('email'),
            'lastName'  => $request->get('last_name'),
            'firstName' => $request->get('first_name'),
            'licence'   => $request->get('licence'),
        );
        $response = $this->userService->create($token, $inputs);
        return $this->buildResponse($response);
    }
}