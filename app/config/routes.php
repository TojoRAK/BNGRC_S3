<?php

use app\controllers\DashboardController;
use app\controllers\UserController;
use app\controllers\DonController;
use app\controllers\BesoinController;
use app\controllers\DispatchController;
use app\controllers\AchatController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/**
 * @var Router $router
 * @var Engine $app
 */
$router->group('', function (Router $router) {

    // LOGIN PAGE: always first
    $router->get('/', function () {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        if (isset($_SESSION['user'])) {
            Flight::redirect('/dashboard');
            return;
        }

        Flight::render('login'); // render login.php
    });

    $router->post('/login', [UserController::class, 'doLogin']);

    $router->get('/logout', function () {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_destroy();
        Flight::redirect('/');
    });

    // DASHBOARD: Admin & Client can access
    $router->get('/dashboard', function () {
        requireAuth(['ADMIN', 'CLIENT']);
        (new DashboardController(Flight::app()))->getSummary();
    });

    $router->get('/region-details', function () {
        requireAuth(['ADMIN', 'CLIENT']);
        (new DashboardController(Flight::app()))->regionDetails();
    });

    // BESOINS: Admin only
    $router->get('/besoins', function () {
        requireAuth(['ADMIN']);
        (new BesoinController())->index();
    });

    $router->post('/besoins', function () {
        requireAuth(['ADMIN']);
        (new BesoinController())->store();
    });

    // API Stats: Admin only
    Flight::route('GET /api/stats', function () {
        requireAuth(['ADMIN']);
        $controller = new DashboardController(Flight::app());
        $controller->getStatsJson();
    });

    // DONS: Admin & Client can view/add
    $router->get('/dons', function () {
        requireAuth(['ADMIN', 'CLIENT']);
        (new DonController())->index();
    });

    $router->post('/dons', function () {
        requireAuth(['ADMIN', 'CLIENT']);
        (new DonController())->store();
    });

    // DISPATCH: Admin only
    $router->get('/dispatch', function () {
        requireAuth(['ADMIN']);
        (new DispatchController())->index();
    });
    $router->post('/dispatch/simulate', function () {
        requireAuth(['ADMIN']);
        (new DispatchController())->simulate();
    });
    $router->post('/dispatch/reset', function () {
        requireAuth(['ADMIN']);
        (new DispatchController())->reset();
    });
    $router->post('/dispatch/validate', function () {
        requireAuth(['ADMIN']);
        (new DispatchController())->validate();
    });

    // ACHATS: Admin only
    $router->get('/achats', function () {
        requireAuth(['ADMIN']);
        (new AchatController(Flight::db()))->index();
    });

    $router->post('/achats', function () {
        requireAuth(['ADMIN']);
        (new AchatController(Flight::db()))->store();
    });

}, [SecurityHeadersMiddleware::class]);


function requireAuth($roles = [])
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    if (!isset($_SESSION['user'])) {
        Flight::redirect('/');
        exit;
    }

    if (!empty($roles) && !in_array($_SESSION['user']['role'], $roles)) {
        $_SESSION['flash_error'] = "Accès non autorisé.";
        Flight::redirect('/dashboard');
        exit;
    }
}


function requireAdmin()
{
    requireAuth(['ADMIN']);
}
