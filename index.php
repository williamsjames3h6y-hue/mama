<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'core/Config.php';
require_once 'core/Database.php';
require_once 'core/Session.php';
require_once 'core/Router.php';
require_once 'core/Auth.php';
require_once 'core/Helper.php';

Session::start();

$db = new Database();
$auth = new Auth($db);
$router = new Router();

$router->get('', function() use ($auth) {
    if (Session::isLoggedIn()) {
        Helper::redirect('/home');
    } else {
        Helper::redirect('/login');
    }
});

$router->get('register', function() {
    if (Session::isLoggedIn()) {
        Helper::redirect('/home');
    }
    include 'views/register.php';
});

$router->post('register', function() use ($auth, $db) {
    if (Session::isLoggedIn()) {
        Helper::redirect('/home');
    }

    $fullName = Helper::post('full_name');
    $email = Helper::post('email');
    $password = Helper::post('password');
    $confirmPassword = Helper::post('confirm_password');

    if ($password !== $confirmPassword) {
        Session::setFlash('error', 'Passwords do not match');
        Helper::redirect('/register');
    }

    $result = $auth->register($email, $password, $fullName);

    if ($result['success']) {
        Session::setFlash('success', 'Registration successful! Please login.');
        Helper::redirect('/login');
    } else {
        Session::setFlash('error', $result['message']);
        Helper::redirect('/register');
    }
});

$router->get('login', function() {
    if (Session::isLoggedIn()) {
        Helper::redirect('/home');
    }
    include 'views/login.php';
});

$router->post('login', function() use ($auth) {
    if (Session::isLoggedIn()) {
        Helper::redirect('/home');
    }

    $email = Helper::post('email');
    $password = Helper::post('password');

    $result = $auth->login($email, $password);

    if ($result['success']) {
        Helper::redirect('/home');
    } else {
        Session::setFlash('error', $result['message']);
        Helper::redirect('/login');
    }
});

$router->get('logout', function() use ($auth) {
    $auth->logout();
    Helper::redirect('/login');
});

$router->get('home', function() use ($auth, $db) {
    $auth->requireLogin();

    $userId = Session::getUserId();
    $users = $db->select('users', ['id' => $userId]);
    $user = $users[0] ?? null;

    if (!$user) {
        Helper::redirect('/logout');
    }

    $payments = $db->query('payments', ['user_id' => $userId]);

    $awaitingPayout = 0;
    $totalPaid = 0;

    foreach ($payments as $payment) {
        if ($payment['status'] === 'pending' && $payment['type'] === 'earning') {
            $awaitingPayout += $payment['amount'];
        } elseif ($payment['status'] === 'completed' && $payment['type'] === 'earning') {
            $totalPaid += $payment['amount'];
        }
    }

    $userProjects = $db->query('user_projects', ['user_id' => $userId]);

    $tasksWeek = 0;
    $payableHours = 0;
    $weekAgo = date('Y-m-d H:i:s', strtotime('-7 days'));

    foreach ($userProjects as $up) {
        if ($up['submitted_at'] >= $weekAgo) {
            $tasksWeek++;
        }
        if ($up['status'] === 'approved') {
            $payableHours += $up['hours_worked'];
        }
    }

    $stats = [
        'awaiting_payout' => $awaitingPayout,
        'total_paid' => $totalPaid,
        'tasks_week' => $tasksWeek,
        'payable_hours' => $payableHours
    ];

    $settingsData = $db->select('site_settings', []);
    $settings = [];
    foreach ($settingsData as $setting) {
        $settings[$setting['key']] = $setting['value'];
    }

    include 'views/home.php';
});

$router->get('projects', function() use ($auth, $db) {
    $auth->requireLogin();

    $userId = Session::getUserId();

    $projects = $db->query('projects', ['status' => 'open']);

    $userProjectsData = $db->query('user_projects', ['user_id' => $userId]);

    $userProjects = [];
    foreach ($userProjectsData as $up) {
        $userProjects[$up['project_id']] = $up;
    }

    include 'views/projects.php';
});

$router->post('projects/apply', function() use ($auth, $db) {
    $auth->requireLogin();

    $userId = Session::getUserId();
    $projectId = Helper::post('project_id');

    $existing = $db->select('user_projects', [
        'user_id' => $userId,
        'project_id' => $projectId
    ]);

    if (!empty($existing)) {
        Session::setFlash('error', 'You have already applied to this project');
        Helper::redirect('/projects');
    }

    $result = $db->insert('user_projects', [
        'user_id' => $userId,
        'project_id' => $projectId,
        'status' => 'submitted'
    ]);

    if ($result) {
        Session::setFlash('success', 'Application submitted successfully');
    } else {
        Session::setFlash('error', 'Failed to submit application');
    }

    Helper::redirect('/projects');
});

$router->get('profile', function() use ($auth, $db) {
    $auth->requireLogin();

    $userId = Session::getUserId();
    $users = $db->select('users', ['id' => $userId]);
    $user = $users[0] ?? null;

    if (!$user) {
        Helper::redirect('/logout');
    }

    $settingsData = $db->select('site_settings', []);
    $settings = [];
    foreach ($settingsData as $setting) {
        $settings[$setting['key']] = $setting['value'];
    }

    include 'views/profile.php';
});

$router->post('profile/deposit', function() use ($auth, $db) {
    $auth->requireLogin();

    $userId = Session::getUserId();
    $amount = Helper::post('amount');

    if ($amount <= 0) {
        Session::setFlash('error', 'Invalid amount');
        Helper::redirect('/profile');
    }

    $result = $db->insert('payments', [
        'user_id' => $userId,
        'type' => 'deposit',
        'amount' => $amount,
        'status' => 'pending',
        'description' => 'Deposit request'
    ]);

    if ($result) {
        Session::setFlash('success', 'Deposit request submitted. Admin will process it soon.');
    } else {
        Session::setFlash('error', 'Failed to submit deposit request');
    }

    Helper::redirect('/profile');
});

$router->post('profile/withdraw', function() use ($auth, $db) {
    $auth->requireLogin();

    $userId = Session::getUserId();
    $amount = Helper::post('amount');

    $users = $db->select('users', ['id' => $userId]);
    $user = $users[0] ?? null;

    if (!$user || $amount > $user['balance']) {
        Session::setFlash('error', 'Insufficient balance');
        Helper::redirect('/profile');
    }

    $settingsData = $db->select('site_settings', ['key' => 'min_withdrawal']);
    $minWithdrawal = $settingsData[0]['value'] ?? 50;

    if ($amount < $minWithdrawal) {
        Session::setFlash('error', 'Amount below minimum withdrawal limit');
        Helper::redirect('/profile');
    }

    $result = $db->insert('payments', [
        'user_id' => $userId,
        'type' => 'withdrawal',
        'amount' => $amount,
        'status' => 'pending',
        'description' => 'Withdrawal request'
    ]);

    if ($result) {
        Session::setFlash('success', 'Withdrawal request submitted. Admin will process it soon.');
    } else {
        Session::setFlash('error', 'Failed to submit withdrawal request');
    }

    Helper::redirect('/profile');
});

$router->get('payments', function() use ($auth, $db) {
    $auth->requireLogin();

    $userId = Session::getUserId();

    $payments = $db->query('payments', ['user_id' => $userId]);

    include 'views/payments.php';
});

$router->get('admin', function() use ($auth, $db) {
    $auth->requireAdmin();

    $allUsers = $db->select('users', []);
    $allProjects = $db->select('projects', []);
    $allPayments = $db->select('payments', []);

    $stats = [
        'total_users' => count($allUsers),
        'active_projects' => count(array_filter($allProjects, fn($p) => $p['status'] === 'open')),
        'pending_payments' => count(array_filter($allPayments, fn($p) => $p['status'] === 'pending')),
        'total_paid' => array_sum(array_map(fn($p) => $p['status'] === 'completed' ? $p['amount'] : 0, $allPayments))
    ];

    $recentActivity = array_slice(array_map(function($payment) use ($allUsers) {
        $user = array_values(array_filter($allUsers, fn($u) => $u['id'] === $payment['user_id']))[0] ?? null;
        return [
            'message' => ($user ? $user['email'] : 'User') . ' - ' . ucfirst($payment['type']) . ' of ' . Helper::formatMoney($payment['amount']),
            'created_at' => $payment['created_at']
        ];
    }, $allPayments), 0, 10);

    include 'views/admin/dashboard.php';
});

$router->get('admin/users', function() use ($auth, $db) {
    $auth->requireAdmin();

    $users = $db->query('users', []);

    include 'views/admin/users.php';
});

$router->get('admin/projects', function() use ($auth, $db) {
    $auth->requireAdmin();

    $projects = $db->query('projects', []);

    include 'views/admin/projects.php';
});

$router->post('admin/projects/add', function() use ($auth, $db) {
    $auth->requireAdmin();

    $result = $db->insert('projects', [
        'title' => Helper::post('title'),
        'description' => Helper::post('description'),
        'rate_min' => Helper::post('rate_min'),
        'rate_max' => Helper::post('rate_max'),
        'project_type' => Helper::post('project_type', 'Remote'),
        'status' => 'open'
    ]);

    if ($result) {
        Session::setFlash('success', 'Project added successfully');
    } else {
        Session::setFlash('error', 'Failed to add project');
    }

    Helper::redirect('/admin/projects');
});

$router->post('admin/projects/delete', function() use ($auth, $db) {
    $auth->requireAdmin();

    $projectId = Helper::post('project_id');

    $result = $db->delete('projects', ['id' => $projectId]);

    if ($result !== false) {
        Session::setFlash('success', 'Project deleted successfully');
    } else {
        Session::setFlash('error', 'Failed to delete project');
    }

    Helper::redirect('/admin/projects');
});

$router->get('admin/payments', function() use ($auth, $db) {
    $auth->requireAdmin();

    $payments = $db->query('payments', [
        'select' => '*',
        'order' => 'created_at.desc'
    ]);

    $allUsers = $db->select('users', []);
    $userMap = [];
    foreach ($allUsers as $user) {
        $userMap[$user['id']] = $user;
    }

    foreach ($payments as &$payment) {
        $payment['user_email'] = $userMap[$payment['user_id']]['email'] ?? 'Unknown';
    }

    include 'views/admin/payments.php';
});

$router->post('admin/payments/approve', function() use ($auth, $db) {
    $auth->requireAdmin();

    $paymentId = Helper::post('payment_id');

    $payments = $db->select('payments', ['id' => $paymentId]);
    $payment = $payments[0] ?? null;

    if (!$payment) {
        Session::setFlash('error', 'Payment not found');
        Helper::redirect('/admin/payments');
    }

    $result = $db->update('payments', ['status' => 'completed'], ['id' => $paymentId]);

    if ($result) {
        $users = $db->select('users', ['id' => $payment['user_id']]);
        $user = $users[0] ?? null;

        if ($user) {
            $newBalance = $user['balance'];

            if ($payment['type'] === 'deposit') {
                $newBalance += $payment['amount'];
            } elseif ($payment['type'] === 'withdrawal') {
                $newBalance -= $payment['amount'];
            } elseif ($payment['type'] === 'earning') {
                $newBalance += $payment['amount'];
                $db->update('users', [
                    'total_earned' => $user['total_earned'] + $payment['amount']
                ], ['id' => $user['id']]);
            }

            $db->update('users', ['balance' => $newBalance], ['id' => $user['id']]);
        }

        Session::setFlash('success', 'Payment approved successfully');
    } else {
        Session::setFlash('error', 'Failed to approve payment');
    }

    Helper::redirect('/admin/payments');
});

$router->post('admin/payments/reject', function() use ($auth, $db) {
    $auth->requireAdmin();

    $paymentId = Helper::post('payment_id');

    $result = $db->update('payments', ['status' => 'failed'], ['id' => $paymentId]);

    if ($result) {
        Session::setFlash('success', 'Payment rejected');
    } else {
        Session::setFlash('error', 'Failed to reject payment');
    }

    Helper::redirect('/admin/payments');
});

$router->get('admin/settings', function() use ($auth, $db) {
    $auth->requireAdmin();

    $settingsData = $db->select('site_settings', []);
    $settings = [];
    foreach ($settingsData as $setting) {
        $settings[$setting['key']] = $setting['value'];
    }

    include 'views/admin/settings.php';
});

$router->post('admin/settings/update', function() use ($auth, $db) {
    $auth->requireAdmin();

    $settingsToUpdate = [
        'site_name', 'contact_email', 'site_currency', 'site_currency_symbol',
        'payment_gateway', 'min_withdrawal', 'referral_bonus'
    ];

    foreach ($settingsToUpdate as $key) {
        $value = Helper::post($key);
        if ($value !== null) {
            $db->update('site_settings', ['value' => $value], ['key' => $key]);
        }
    }

    Session::setFlash('success', 'Settings updated successfully');
    Helper::redirect('/admin/settings');
});

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_GET['url'] ?? '';

$router->dispatch($method, $uri);
