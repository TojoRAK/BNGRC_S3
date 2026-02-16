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
        $dons = $model->getTotalDons();
        $besoins = $model->getBesoinsTotaux();
        $reste = $besoins - $dons;
        $donsFormated = $model->formatDeviseAr($dons);
        $besoinsFormated = $model->formatDeviseAr($besoins);
        $resteFormated = $model->formatDeviseAr($reste);
        $details = $model->getDetails();
        Flight::render('dashboard/index',[
            'dons' => $donsFormated,
            'besoins' =>$besoinsFormated,
            'reste' => $resteFormated,
            'details' => $details
        ]);
    }



}
