<?php

namespace app\controllers;


use app\models\DispatchModel;
use app\models\AchatModel;
use Flight;

class DispatchController
{
    private DispatchModel $model;

    public function __construct()
    {
        $this->model = new DispatchModel(Flight::db());
    }

    public function index()
    {
        $dons = $this->model->getDonDisponible();
        $besoins = $this->model->getBesoinOuvert();

        $data = [
            'dons' => $dons,
            'besoins' => $besoins,
            'allocations' => [],
            'donRemaining' => [],
            'besoinRemaining' => [],
            'stats' => [
                'nb_dons' => count($dons),
                'nb_besoins' => count($besoins),
                'nb_allocations' => 0,
                'total_don' => 0,
                'total_besoin' => 0,
                'total_attribue' => 0,
                'coverage_percent' => 0,
            ],
        ];

        Flight::render('dispatch', $data);
        exit;
    }

    public function simulate()
    {
        $mode = isset($_POST['mode_dispatch']) ? trim((string) $_POST['mode_dispatch']) : '1';

        if ($mode === '2') {
            $data = $this->model->simulateDispatch("BESOIN_CROISSANT");
        } elseif ($mode==='1') {
            $data = $this->model->simulateDispatch();
        }elseif($mode ==='3'){
            $data = $this->model->simulateDispatch('PRORATA');
        }

        if (empty($data) || !isset($data['dons'], $data['besoins'])) {
            $dons = $this->model->getDonDisponible();
            $besoins = $this->model->getBesoinOuvert();
            $data = [
                'dons' => $dons,
                'besoins' => $besoins,
                'allocations' => [],
                'donRemaining' => [],
                'besoinRemaining' => [],
                'stats' => [
                    'nb_dons' => count($dons),
                    'nb_besoins' => count($besoins),
                    'nb_allocations' => 0,
                    'total_don' => 0,
                    'total_besoin' => 0,
                    'total_attribue' => 0,
                    'coverage_percent' => 0,
                ],
            ];
        }
        Flight::render('dispatch', $data);

        exit;
    }

    public function validate()
    {
        try {
            $simulation = $this->model->simulateDispatch();

            $result = $this->model->validateSimulation($simulation);



        } catch (\Throwable $e) {

            Flight::flash('error', "Erreur lors de la validation du dispatch : " . $e->getMessage());
        }

        Flight::redirect('/dispatch');
        exit;
    }



    public function reset()
    {
        Flight::redirect('/dispatch');
        exit;
        // $this->index();  
    }

    public function validateAchat()
    {
        $id_achat = $_POST['id_achat'] ?? null;
        if (!$id_achat) {
            Flight::halt(400, "id_achat manquant.");
        }

        try {
            $achatModel = new AchatModel(Flight::db());
            $achatModel->dispatcherAchat((int)$id_achat);
            Flight::redirect('/achats');
        } catch (\Exception $e) {
            Flight::halt(500, $e->getMessage());
        }
    }
}
