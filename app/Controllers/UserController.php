<?php

namespace App\Controllers;

use App\Models\User;

class UserController extends Controller
{
    private $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function index($user)
    {
        if ($user['role'] !== 'admin') {
            return $this->error('Forbidden: Admin access required', 403);
        }

        try {
            $users = $this->userModel->all(['order' => 'created_at.desc']);
            return $this->json($users);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch users', 500);
        }
    }

    public function show($id, $user)
    {
        if ($user['role'] !== 'admin' && $user['id'] !== $id) {
            return $this->error('Forbidden', 403);
        }

        try {
            $userData = $this->userModel->find($id);

            if (!$userData) {
                return $this->error('User not found', 404);
            }

            unset($userData['password_hash']);

            return $this->json($userData);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch user', 500);
        }
    }

    public function update($id, $user)
    {
        if ($user['role'] !== 'admin' && $user['id'] !== $id) {
            return $this->error('Forbidden', 403);
        }

        $data = $this->getJsonInput();

        if ($user['role'] !== 'admin') {
            unset($data['role']);
            unset($data['vip_level']);
            unset($data['balance']);
        }

        try {
            $updatedUser = $this->userModel->update($id, $data);

            if (!$updatedUser) {
                return $this->error('User not found', 404);
            }

            unset($updatedUser['password_hash']);

            return $this->json($updatedUser);
        } catch (\Exception $e) {
            return $this->error('Failed to update user', 500);
        }
    }

    public function destroy($id, $user)
    {
        if ($user['role'] !== 'admin') {
            return $this->error('Forbidden: Admin access required', 403);
        }

        try {
            $this->userModel->delete($id);
            return $this->json(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return $this->error('Failed to delete user', 500);
        }
    }
}
