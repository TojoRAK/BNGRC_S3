<?php

namespace app\controllers;

use app\models\TradeOffer;
use app\models\TradeFinalizer;
use Exception;
use Flight;
use app\models\CategorieModel;

class TradeController
{
    private $app;

    public function __construct($app = null)
    {
        $this->app = $app;
    }
    public function makeRequest()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']['id'])) {
            echo json_encode(['ok' => false, 'message' => 'Non authentifié']);
            return;
        }

        $requesterId = $_SESSION['user']['id'];

        $wantedId = $_POST['wanted_id'] ?? null;
        $offeredId = $_POST['offered_id'] ?? null;
        $message = $_POST['message'] ?? null;

        if ($wantedId === null || $offeredId === null) {
            echo json_encode(['ok' => false, 'message' => 'Paramètres manquants (wanted_id, offered_id)']);
            return;
        }

        $model = new TradeOffer(Flight::db());
        try {
            $proposition = $model->requestObject($requesterId, $wantedId, $offeredId, $message);
            if ($proposition > 0) {
                echo json_encode(['ok' => true]);
                return;
            }
            echo json_encode(['ok' => false, 'message' => 'Problème lors de la demande']);

        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    public function accept($id)
    {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user']['id'])) {
            echo json_encode(['ok' => false, 'message' => 'Non authentifié']);
            return;
        }

        try {
            $model = new TradeFinalizer(Flight::db());
            $model->accept((int) $id, (int) $_SESSION['user']['id']);
            echo json_encode(['ok' => true]);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    public function reject($id)
    {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user']['id'])) {
            echo json_encode(['ok' => false, 'message' => 'Non authentifié']);
            return;
        }

        try {
            $model = new TradeFinalizer(Flight::db());
            $model->reject((int) $id, (int) $_SESSION['user']['id']);
            echo json_encode(['ok' => true]);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    public function cancel($id)
    {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user']['id'])) {
            echo json_encode(['ok' => false, 'message' => 'Non authentifié']);
            return;
        }

        try {
            $model = new TradeFinalizer(Flight::db());
            $model->cancel((int) $id, (int) $_SESSION['user']['id']);
            echo json_encode(['ok' => true]);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
        }
    }


}