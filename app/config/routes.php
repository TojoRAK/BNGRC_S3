<?php

// use Flight;

use app\controllers\DashboardController;
use app\controllers\UserController;
use app\controllers\DonController;
use app\controllers\BesoinController;
use app\controllers\DispatchController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/**
 * @var Router $router
 * @var Engine $app
 */
$router->group('', function (Router $router) {


	$router->get('/', [DashboardController::class ,'getSummary']);
	$router->get('/filtrer',[DashboardController::class , 'getSummary']);
	
	$router->get('/', function () {
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}

		
		if (isset($_SESSION['user'])) {
			Flight::redirect('/dashboard');
			return;
		}

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

	
	$router->get('/dashboard', function () {
		requireAuth();
		(new DashboardController(Flight::app()))->getSummary();
	});

	$router->get('/region-details', function () {
		// requireAuth();
		(new DashboardController(Flight::app()))->regionDetails();
	});

	
	$router->get('/besoins', function () {
		(new BesoinController())->index();
	});

	$router->post('/besoins', function () {
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
	
	$router->get('/dispatch', [DispatchController::class, 'index']);
	$router->post('/dispatch/simulate', [DispatchController::class, 'simulate']);
	$router->post('/dispatch/reset', [DispatchController::class, 'reset']);
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
		Flight::redirect('/dashboard');
		exit;
	}
}
