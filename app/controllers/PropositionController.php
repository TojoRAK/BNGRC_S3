<?php

namespace app\controllers;

use app\models\PropositionModel;
use Exception;
use Flight;
use app\models\CategorieModel;

class PropositionController
{
    private $app;

    public function __construct($app = null)
    {
        $this->app = $app;
    }
    public function getReceivedPropositions()
    {
        try {
            $model = new PropositionModel(Flight::db());
            $propositions = $model->getPropositions($_SESSION['user']['id']);
            Flight::render('client/propositions', [
                'propositions' => $propositions
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
        }
    }
    public function getHistoriquePropositions()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $model = new PropositionModel(Flight::db());
            $propositions = $model->getHistorique();

            echo json_encode([
                'ok' => true,
                'data' => $propositions,
                'count' => count($propositions),
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'message' => 'Erreur lors de la récupération des catégories',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function showHistorique()
    {
        try {
            $model = new PropositionModel(Flight::db());
            $echanges = $model->getHistorique();
            Flight::render('client/histo_propositions', [
                'echanges' => $echanges,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
        }
    }



}