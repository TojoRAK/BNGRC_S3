<?php

namespace app\controllers;

use app\models\StatisticModel;
use Exception;
use Flight;
use flight\Engine;

class StatController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function showDash()
    {
        $stat = new StatisticModel(Flight::db());

        Flight::render('admin/dashboard', [
            "nbObjet" =>  $stat->countTotalObjet(),
            "nbUser" => $stat->countTotalUsers(),
            "nbEchange" => $stat->countTotalEchange()
        ]);
        exit;
    }
}
