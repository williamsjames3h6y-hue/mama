<?php

namespace App\Controllers;

use App\Services\AuthService;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register()
    {
        $data = $this->getJsonInput();

        if (!isset($data['email']) || !isset($data['password']) || !isset($data['full_name'])) {
            return $this->error('Email, password, and full name are required', 400);
        }

        try {
            $user = $this->authService->register(
                $data['email'],
                $data['password'],
                $data['full_name'],
                $data['referral_code'] ?? null
            );

            $loginResult = $this->authService->login($data['email'], $data['password']);

            return $this->json($loginResult, 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function login()
    {
        $data = $this->getJsonInput();

        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->error('Email and password are required', 400);
        }

        try {
            $result = $this->authService->login($data['email'], $data['password']);
            return $this->json($result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 401);
        }
    }

    public function me($user)
    {
        return $this->json(['user' => $user]);
    }
}
