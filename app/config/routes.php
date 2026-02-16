<?php

// use Flight;

use app\controllers\AdminLogController;
use app\controllers\CategorieController;
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

	// ============ PUBLIC ============

	$router->get('/', function () {
		Flight::render('client/login');
	});

	$router->group('/auth', function () use ($router) {

		$router->get('/register', function () {
			Flight::render('client/register');
		});

		$router->post('/register', [AuthClient::class, 'validateInputAndLogin']);
		$router->post('/login', [AuthClient::class, 'doLogin']);

		$router->get('/logout', function () {
			requireAuth();
			(new AuthClient(Flight::app()))->doLogout();
		});
	});

	// ============ USER PROTECTED ============

	$router->get('/index', function () {
		requireAuth();
		(new ObjetController())->index();
	});

	$router->get('/objet/@id:[0-9]+', function ($id) {
		requireAuth();
		(new ObjetController())->show($id);
	});

	$router->get('/my-objets', function () {
		requireAuth();
		(new ObjetController())->myObjets();
	});

	$router->group('/objets', function () use ($router) {

		$router->get('/list', function () {
			requireAuth();
			(new ObjetController())->list();
		});
		$router->get('/list/filter', function () {
			requireAuth();
			(new ObjetController())->filter();

		});

		$router->get('/@id:[0-9]+', function ($id) {
			requireAuth();
			(new ObjetController())->getOne($id);
		});

		$router->post('', function () {
			requireAuth();
			(new ObjetController())->create();
		});

		$router->post('/@id:[0-9]+', function ($id) {
			requireAuth();
			(new ObjetController())->update($id);
		});

		$router->delete('/@id:[0-9]+', function ($id) {
			requireAuth();
			(new ObjetController())->delete($id);
		});

		$router->get('/@id:[0-9]+/images', function ($id) {
			requireAuth();
			(new ObjetController())->getImages($id);
		});

		$router->delete('/images/@imageId:[0-9]+', function ($imageId) {
			requireAuth();
			(new ObjetController())->deleteImage($imageId);
		});

		$router->post('/images/@imageId:[0-9]+/set-main/@objetId:[0-9]+', function ($imageId, $objetId) {
			requireAuth();
			(new ObjetController())->setMainImage($imageId, $objetId);
		});
	});

	$router->group('/propositions', function () use ($router) {

		$router->get('/list', function () {
			requireAuth();
			(new PropositionController())->getReceivedPropositions();
		});

		$router->get('/historique', function () {
			requireAuth();
			(new PropositionController())->showHistorique();
		});

		$router->post('/@id:[0-9]+/accept', function ($id) {
			requireAuth();
			(new TradeController())->accept($id);
		});

		$router->post('/@id:[0-9]+/reject', function ($id) {
			requireAuth();
			(new TradeController())->reject($id);
		});

		$router->post('/@id:[0-9]+/cancel', function ($id) {
			requireAuth();
			(new TradeController())->cancel($id);
		});
	});

	$router->group('/trade', function () use ($router) {

		$router->post('/request', function () {
			requireAuth();
			(new TradeController())->makeRequest();
		});
	});

	// ============ ADMIN ============

	$router->group('/admin', function () use ($router) {

		$router->get('/login', function () {
			Flight::render('admin/login');
		});

		$router->post('/login', [AdminLogController::class, 'doLogin']);

		$router->get('/logout', function () {
			requireAdmin();
			(new AdminLogController(Flight::app()))->doLogout();
		});

		$router->get('/', function () {
			if (session_status() !== PHP_SESSION_ACTIVE)
				session_start();
			Flight::redirect(isset($_SESSION['admin']) ? '/admin/dash' : '/admin/login');
		});

		$router->get('/dash', function () {
			requireAdmin();
			(new StatController(Flight::app()))->showDash();
		});

		$router->get('/categories', function () {
			requireAdmin();
			Flight::render('admin/categories');
		});

		$router->group('/categories', function () use ($router) {

			$router->get('/list', function () {
				requireAdmin();
				(new CategorieController())->getAllCategories();
			});

			$router->get('/@id:[0-9]+', function ($id) {
				requireAdmin();
				(new CategorieController())->getCategory($id);
			});

			$router->post('', function () {
				requireAdmin();
				(new CategorieController())->createCategory();
			});

			$router->post('/@id:[0-9]+', function ($id) {
				requireAdmin();
				(new CategorieController())->updateCategory($id);
			});

			$router->delete('/@id:[0-9]+', function ($id) {
				requireAdmin();
				(new CategorieController())->deleteCategory($id);
			});
		});
	});

}, [SecurityHeadersMiddleware::class]);


function requireAdmin(): void
{
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	// Pas connecté
	if (!isset($_SESSION['admin'])) {
		$_SESSION['flash_error'] = "Veuillez vous connecter.";
		Flight::redirect('/admin/login');
		exit;
	}

	// if (($_SESSION['admin']['role'] ?? '') !== 'ADMIN') {
	//     unset($_SESSION['admin']);
	//     $_SESSION['flash_error'] = "Accès refusé.";
	//     Flight::redirect('/admin/login');
	//     exit;
	// }
}
function requireAuth()
{
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	if (!isset($_SESSION['user'])) {
		$_SESSION['flash_error'] = "Veuillez vous connecter.";
		Flight::redirect('/');
		exit;
	}
}
