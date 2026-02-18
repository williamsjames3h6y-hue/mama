<?php

namespace App\Controllers;

use App\Models\UserProject;

class UserProjectController extends Controller
{
    private $userProjectModel;

    public function __construct(UserProject $userProjectModel)
    {
        $this->userProjectModel = $userProjectModel;
    }

    public function index($user)
    {
        try {
            $userId = $this->getQueryParam('user_id');

            if ($userId) {
                if ($user['role'] !== 'admin' && $user['id'] !== $userId) {
                    return $this->error('Forbidden', 403);
                }

                $projects = $this->userProjectModel->getByUser($userId);
            } else {
                if ($user['role'] !== 'admin') {
                    return $this->error('Forbidden', 403);
                }

                $projects = $this->userProjectModel->all(['order' => 'submitted_at.desc']);
            }

            return $this->json($projects);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch user projects', 500);
        }
    }

    public function show($id, $user)
    {
        try {
            $userProject = $this->userProjectModel->find($id);

            if (!$userProject) {
                return $this->error('User project not found', 404);
            }

            if ($user['role'] !== 'admin' && $user['id'] !== $userProject['user_id']) {
                return $this->error('Forbidden', 403);
            }

            return $this->json($userProject);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch user project', 500);
        }
    }

    public function store($user)
    {
        $data = $this->getJsonInput();

        if (!isset($data['project_id']) || !isset($data['hours_worked']) || !isset($data['amount_earned'])) {
            return $this->error('Project ID, hours worked, and amount earned are required', 400);
        }

        try {
            $projectData = [
                'user_id' => $user['id'],
                'project_id' => $data['project_id'],
                'hours_worked' => $data['hours_worked'],
                'amount_earned' => $data['amount_earned'],
                'status' => 'submitted'
            ];

            $userProject = $this->userProjectModel->create($projectData);

            return $this->json($userProject, 201);
        } catch (\Exception $e) {
            return $this->error('Failed to submit project', 500);
        }
    }

    public function update($id, $user)
    {
        if ($user['role'] !== 'admin') {
            return $this->error('Forbidden: Admin access required', 403);
        }

        $data = $this->getJsonInput();

        try {
            if (isset($data['status']) && $data['status'] === 'approved') {
                $userProject = $this->userProjectModel->find($id);
                if (!$userProject) {
                    return $this->error('User project not found', 404);
                }

                $updated = $this->userProjectModel->approveProject($id, $userProject['user_id']);
                return $this->json($updated);
            }

            $updated = $this->userProjectModel->update($id, $data);

            if (!$updated) {
                return $this->error('User project not found', 404);
            }

            return $this->json($updated);
        } catch (\Exception $e) {
            return $this->error('Failed to update user project: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id, $user)
    {
        if ($user['role'] !== 'admin') {
            return $this->error('Forbidden: Admin access required', 403);
        }

        try {
            $this->userProjectModel->delete($id);
            return $this->json(['message' => 'User project deleted successfully']);
        } catch (\Exception $e) {
            return $this->error('Failed to delete user project', 500);
        }
    }
}
