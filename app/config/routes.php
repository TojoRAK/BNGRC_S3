<?php

// use Flight;

use app\controllers\AdminLogController;
use app\controllers\CategorieController;
use app\controllers\DashboardController;
use app\controllers\PropositionController;

use app\controllers\AuthClient;
use app\controllers\BesoinController;
use app\controllers\DonController;
use app\controllers\UserController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/**
 * @var Router $router
 * @var Engine $app
 */

$router->group('', function (Router $router) {


	$router->get('/', [DashboardController::class ,'getSummary']);
	

	$router->get('/', function () {
		Flight::render('login');
	});

	$router->post('/login', [UserController::class, 'doLogin']);

	$router->get('/logout', function () {
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}

		session_destroy();
		Flight::redirect('/');
	});


	$router->get('/accueil', function () {
		requireAuth();
		echo "<h2>Accueil Client</h2>";
	});

	$router->get('/admin/dashboard', function () {
		requireAdmin();
		echo "<h2>Dashboard Admin</h2>";
	});


	$router->get('/besoins', function () {
		requireAuth();
		(new BesoinController())->index();
	});

	$router->post('/besoins', function () {
		requireAdmin();
		(new BesoinController())->store();
	});



	$router->get('/dons', function () {
		requireAuth();
		(new DonController())->index();
	});

	$router->post('/dons', function () {
		requireAdmin();
		(new DonController())->store();
	});
}, [SecurityHeadersMiddleware::class]);

function requireAuth()
{
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	if (!isset($_SESSION['user'])) {
		Flight::redirect('/');
		exit;
	}
}

function requireAdmin()
{
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
		$_SESSION['flash_error'] = "Accès réservé aux administrateurs.";
		Flight::redirect('/');
		exit;
	}
}
