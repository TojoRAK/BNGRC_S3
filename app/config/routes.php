<?php

// use Flight;

use app\controllers\AdminLogController;
use app\controllers\CategorieController;
use app\controllers\DashboardController;
use app\controllers\PropositionController;

use app\controllers\AuthClient;
use app\controllers\ObjetController;
use app\controllers\StatController;
use app\controllers\TradeController;
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
	


}, [SecurityHeadersMiddleware::class]);

