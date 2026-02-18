<?php

namespace App\Controllers;

use App\Models\Payment;

class PaymentController extends Controller
{
    private $paymentModel;

    public function __construct(Payment $paymentModel)
    {
        $this->paymentModel = $paymentModel;
    }

    public function index($user)
    {
        try {
            $userId = $this->getQueryParam('user_id');

            if ($userId) {
                if ($user['role'] !== 'admin' && $user['id'] !== $userId) {
                    return $this->error('Forbidden', 403);
                }

                $payments = $this->paymentModel->getByUser($userId);
            } else {
                if ($user['role'] !== 'admin') {
                    return $this->error('Forbidden', 403);
                }

                $payments = $this->paymentModel->all(['order' => 'created_at.desc']);
            }

            return $this->json($payments);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch payments', 500);
        }
    }

    public function show($id, $user)
    {
        try {
            $payment = $this->paymentModel->find($id);

            if (!$payment) {
                return $this->error('Payment not found', 404);
            }

            if ($user['role'] !== 'admin' && $user['id'] !== $payment['user_id']) {
                return $this->error('Forbidden', 403);
            }

            return $this->json($payment);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch payment', 500);
        }
    }
}
