<?php

namespace App\Services\Authentication;

class AuthenticationService
{
    protected $authService;

    public function __construct(AuthenticationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function register($credentials)
    {
        return $this->authService->register($credentials);
    }

    public function login($credentials)
    {
        return $this->authService->login($credentials);
    }

    public function authenticate($credentials)
    {
        return $this->authService->authenticate($credentials);
    }

    public function refreshToken($request)
    {
        return $this->authService->refreshToken($request);
    }

    public function logout($user)
    {
        return $this->authService->logout($user);
    }
}
