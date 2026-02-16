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

    public function getSummary()
    {
        $model = new DashboardModel(Flight::db());
        $dons = $model->getTotalDons();
        $besoins = $model->getBesoinsTotaux();
        $reste = $besoins - $dons;
        $donsFormated = $model->formatDeviseAr($dons);
        $besoinsFormated = $model->formatDeviseAr($besoins);
        $resteFormated = $model->formatDeviseAr($reste);
        $region = isset($_GET['region']) ? trim((string) $_GET['region']) : "";
        $ville = isset($_GET['ville']) ? trim((string) $_GET['ville']) : "";

        $regionId = ($region !== '' && ctype_digit($region)) ? (int) $region : null;
        $villeId = ($ville !== '' && ctype_digit($ville)) ? (int) $ville : null;

        $regions = $model->getRegions();
        $villes = $model->getVilles($regionId);
        $details = [];

        if (empty($region) && empty($ville)) {
            $details = $model->getDetails();
        } else {
            $details = $model->filter($regionId, $villeId, "");
        }
        Flight::render('dashboard/index', [
            'dons' => $donsFormated,
            'besoins' => $besoinsFormated,
            'reste' => $resteFormated,
            'details' => $details,
            'regions' => $regions,
            'villes' => $villes,
            'filters' => [
                'region' => $region,
                'ville' => $ville,
            ],
        ]);
    }
}
