<?php

namespace App\Controllers;

use App\Models\Withdrawal;
use App\Models\SiteSetting;

class WithdrawalController extends Controller
{
    private $withdrawalModel;
    private $settingModel;

    public function __construct(Withdrawal $withdrawalModel, SiteSetting $settingModel)
    {
        $this->withdrawalModel = $withdrawalModel;
        $this->settingModel = $settingModel;
    }

    public function index($user)
    {
        try {
            $userId = $this->getQueryParam('user_id');

            if ($userId) {
                if ($user['role'] !== 'admin' && $user['id'] !== $userId) {
                    return $this->error('Forbidden', 403);
                }

                $withdrawals = $this->withdrawalModel->getByUser($userId);
            } else {
                if ($user['role'] !== 'admin') {
                    return $this->error('Forbidden', 403);
                }

                $withdrawals = $this->withdrawalModel->all(['order' => 'created_at.desc']);
            }

            return $this->json($withdrawals);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch withdrawals', 500);
        }
    }

    public function show($id, $user)
    {
        try {
            $withdrawal = $this->withdrawalModel->find($id);

            if (!$withdrawal) {
                return $this->error('Withdrawal not found', 404);
            }

            if ($user['role'] !== 'admin' && $user['id'] !== $withdrawal['user_id']) {
                return $this->error('Forbidden', 403);
            }

            return $this->json($withdrawal);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch withdrawal', 500);
        }
    }

    public function store($user)
    {
        $data = $this->getJsonInput();

        if (!isset($data['amount']) || !isset($data['method']) || !isset($data['account_details'])) {
            return $this->error('Amount, method, and account details are required', 400);
        }

        try {
            $minWithdrawalSetting = $this->settingModel->getByKey('min_withdrawal');
            $minAmount = $minWithdrawalSetting ? floatval($minWithdrawalSetting['value']) : 50;

            if ($data['amount'] < $minAmount) {
                return $this->error("Minimum withdrawal amount is $" . $minAmount, 400);
            }

            if ($user['balance'] < $data['amount']) {
                return $this->error('Insufficient balance', 400);
            }

            $withdrawalData = [
                'user_id' => $user['id'],
                'amount' => $data['amount'],
                'method' => $data['method'],
                'account_details' => json_encode($data['account_details']),
                'status' => 'pending'
            ];

            $withdrawal = $this->withdrawalModel->create($withdrawalData);

            return $this->json($withdrawal, 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create withdrawal: ' . $e->getMessage(), 500);
        }
    }

    public function update($id, $user)
    {
        if ($user['role'] !== 'admin') {
            return $this->error('Forbidden: Admin access required', 403);
        }

        $data = $this->getJsonInput();

        try {
            if (isset($data['status'])) {
                $withdrawal = $this->withdrawalModel->processWithdrawal($id, $data['status']);
                return $this->json($withdrawal);
            }

            $withdrawal = $this->withdrawalModel->update($id, $data);

            if (!$withdrawal) {
                return $this->error('Withdrawal not found', 404);
            }

            return $this->json($withdrawal);
        } catch (\Exception $e) {
            return $this->error('Failed to update withdrawal: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id, $user)
    {
        if ($user['role'] !== 'admin') {
            return $this->error('Forbidden: Admin access required', 403);
        }

        try {
            $this->withdrawalModel->delete($id);
            return $this->json(['message' => 'Withdrawal deleted successfully']);
        } catch (\Exception $e) {
            return $this->error('Failed to delete withdrawal', 500);
        }
    }
}
