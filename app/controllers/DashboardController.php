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
        $dons = $model->getTotalDispatched();
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

    public function regionDetails()
    {
        $region = isset($_GET['region']) ? trim((string) $_GET['region']) : '';
        if ($region === '' || !ctype_digit($region)) {
            Flight::redirect('/dashboard');
            return;
        }

        $regionId = (int) $region;
        $model = new DashboardModel(Flight::db());

        $regionRow = $model->getRegionById($regionId);
        if (!$regionRow) {
            Flight::notFound();
            return;
        }

        $details = $model->getBesoinsDetailsByRegion($regionId);
        $totalMontant = 0;
        foreach ($details as $row) {
            $totalMontant += (float) ($row['montant_total'] ?? 0);
        }

        Flight::render('dashboard/region_details', [
            'region' => $regionRow,
            'details' => $details,
            'totalMontant' => $totalMontant,
        ]);
    }
    public function getStatsJson()
    {
        header('Content-Type: application/json');
        $model = new DashboardModel(Flight::db());

        $besoins = $model->getBesoinsTotaux();
        $satisfaits = $model->getTotalDispatched(); 
        $reste = $besoins - $satisfaits;

        Flight::json([
            'besoins_totaux' => $model->formatDeviseAr($besoins),
            'besoins_satisfaits' => $model->formatDeviseAr($satisfaits),
            'besoins_restants' => $model->formatDeviseAr($reste)
        ]);
    }
}
