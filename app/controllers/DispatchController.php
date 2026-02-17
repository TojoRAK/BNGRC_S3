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
        $data = $this->model->simulateDispatch();
        Flight::render('dispatch', $data);

        exit;
    }


    public function reset()
    {
        $this->index();
    }

    public function validate()
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
