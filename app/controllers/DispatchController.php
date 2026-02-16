<?php

namespace app\controllers;


use app\models\DispatchModel;
use Flight;
use PDO;

class DispatchController
{
    private DispatchModel $model;

    public function __construct(PDO $pdo)
    {
        $this->model = new DispatchModel($pdo);
    }

    public function index()
    {
        $dons = $this->model->getDonDisponible();
        $besoins = $this->model->getBesoinOuvert();


        $stats = [
            'nb_dons' => count($dons),
            'nb_besoins' => count($besoins),
            'nb_allocations' => 0,
            'total_don' => 0,
            'total_besoin' => 0,
            'total_attribue' => 0,
            'coverage_percent' => 0,
        ];

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        Flight::render('dispatch', $stats);
        exit;
    }

    public function simulate()
    {
        $result = $this->model->simulateDispatch();
        Flight::render('dispatch', $result);

        exit;
    }


    public function reset()
    {
        Flight::render('dispatch', []);
        exit;
    }
}
