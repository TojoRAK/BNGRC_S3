<?php

// use Flight;

use app\controllers\BesoinController;
use app\controllers\DonController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/**
 * @var Router $router
 * @var Engine $app
 */
$router->group('', function (Router $router) {


	$router->get('/besoins', function () {
		(new BesoinController())->index();
	});

	$router->post('/besoins', function () {
		(new BesoinController())->store();
	});

	$router->get('/dons', function () {
		(new DonController())->index();
	});

	$router->post('/dons', function () {
		(new DonController())->store();
	});
}, [SecurityHeadersMiddleware::class]);
