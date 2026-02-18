<?php

namespace App\Models;

class UserProject extends Model
{
    protected $table = 'user_projects';

    protected $fillable = [
        'user_id',
        'project_id',
        'status',
        'hours_worked',
        'amount_earned'
    ];

    public function getByUser($userId)
    {
        return $this->supabase->query($this->table, [
            'eq' => ['user_id' => $userId],
            'order' => 'submitted_at.desc'
        ]);
    }

    public function getByProject($projectId)
    {
        return $this->supabase->query($this->table, [
            'eq' => ['project_id' => $projectId],
            'order' => 'submitted_at.desc'
        ]);
    }

    public function approveProject($id, $userId)
    {
        $userProject = $this->find($id);
        if (!$userProject) {
            throw new \Exception('User project not found');
        }

        $updatedProject = $this->update($id, ['status' => 'approved']);

        $user = $this->supabase->query('users', [
            'eq' => ['id' => $userId],
            'limit' => 1
        ]);

        if (!empty($user)) {
            $userData = $user[0];
            $this->supabase->update('users', [
                'balance' => $userData['balance'] + $userProject['amount_earned'],
                'total_earned' => $userData['total_earned'] + $userProject['amount_earned']
            ], ['id' => $userId]);

            $project = $this->supabase->query('projects', [
                'eq' => ['id' => $userProject['project_id']],
                'limit' => 1
            ]);

            $projectTitle = !empty($project) ? $project[0]['title'] : 'Unknown Project';

            $this->supabase->insert('payments', [
                'user_id' => $userId,
                'type' => 'earning',
                'amount' => $userProject['amount_earned'],
                'status' => 'completed',
                'description' => 'Payment for project: ' . $projectTitle
            ]);
        }

        return $updatedProject;
    }
}
