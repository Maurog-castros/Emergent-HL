<?php
/**
 * Controlador del Dashboard
 * LogiSystem
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Client.php';

class DashboardController
{
    public function index(): void
    {
        $userModel   = new User();
        $clientModel = new Client();

        $userStats   = $userModel->countAll();
        $clientStats = $clientModel->countAll();

        $pageTitle    = t('dashboard');
        $activeModule = 'dashboard';

        require __DIR__ . '/../views/dashboard/index.php';
    }
}
