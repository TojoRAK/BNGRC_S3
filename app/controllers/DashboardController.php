<?php

namespace app\controllers;

use app\models\CategorieModel;
use app\models\DashboardModel;
use app\models\UserModel;
use Exception;
use Flight;
use flight\Engine;

class DashboardController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getSummary(){
        $model = new DashboardModel(Flight::db());
        $dons = $model->formatDeviseAr($model->getTotalDons());
        $besoins = $model->formatDeviseAr($model->getBesoinsTotaux());

        Flight::render('dashboard/index',[
            'dons' => $dons,
            'besoins' =>$besoins
        ]);
    }
}
