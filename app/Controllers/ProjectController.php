<?php

namespace App\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    private $projectModel;

    public function __construct(Project $projectModel)
    {
        $this->projectModel = $projectModel;
    }

    public function index($user = null)
    {
        try {
            $vipLevel = $this->getQueryParam('vipLevel');

            if ($vipLevel) {
                $projects = $this->projectModel->getByVipLevel(intval($vipLevel));
            } else {
                if ($user && $user['role'] === 'admin') {
                    $projects = $this->projectModel->all(['order' => 'created_at.desc']);
                } else {
                    $projects = $this->projectModel->getOpen();
                }
            }

            return $this->json($projects);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch projects', 500);
        }
    }

    public function show($id)
    {
        try {
            $project = $this->projectModel->find($id);

            if (!$project) {
                return $this->error('Project not found', 404);
            }

            return $this->json($project);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch project', 500);
        }
    }

    public function store($user)
    {
        if ($user['role'] !== 'admin') {
            return $this->error('Forbidden: Admin access required', 403);
        }

        $data = $this->getJsonInput();

        if (!isset($data['title']) || !isset($data['description']) ||
            !isset($data['rate_min']) || !isset($data['rate_max']) ||
            !isset($data['vip_level_required'])) {
            return $this->error('Title, description, rate min, rate max, and VIP level are required', 400);
        }

        try {
            $projectData = [
                'title' => $data['title'],
                'description' => $data['description'],
                'rate_min' => $data['rate_min'],
                'rate_max' => $data['rate_max'],
                'vip_level_required' => $data['vip_level_required'],
                'project_type' => $data['project_type'] ?? 'Remote',
                'status' => $data['status'] ?? 'open'
            ];

            $project = $this->projectModel->create($projectData);

            return $this->json($project, 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create project', 500);
        }
    }

    public function update($id, $user)
    {
        if ($user['role'] !== 'admin') {
            return $this->error('Forbidden: Admin access required', 403);
        }

        $data = $this->getJsonInput();

        try {
            $project = $this->projectModel->update($id, $data);

            if (!$project) {
                return $this->error('Project not found', 404);
            }

            return $this->json($project);
        } catch (\Exception $e) {
            return $this->error('Failed to update project', 500);
        }
    }

    public function destroy($id, $user)
    {
        if ($user['role'] !== 'admin') {
            return $this->error('Forbidden: Admin access required', 403);
        }

        try {
            $this->projectModel->delete($id);
            return $this->json(['message' => 'Project deleted successfully']);
        } catch (\Exception $e) {
            return $this->error('Failed to delete project', 500);
        }
    }
}
