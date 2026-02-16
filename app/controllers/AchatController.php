<?php

namespace app\controllers;

use app\models\AchatModel;
use Exception;
use Flight;

class AchatController {

    private $model;

    public function __construct() {
        $this->model = new AchatModel(Flight::db());
    }

  
    public function index() {

        $villes   = $this->model->getVilles();
        $articles = $this->model->getArticles();
        $besoins  = $this->model->listBesoins();
        $achats   = $this->model->listAchats();

        Flight::render('achats', compact(
            'villes',
            'articles',
            'besoins',
            'achats'
        ));
    }


    public function store() {

        $id_ville   = $_POST['ville_id'] ?? null;
        $id_article = $_POST['article_id'] ?? null;
        $quantite   = isset($_POST['quantite']) ? (float)$_POST['quantite'] : 0;

        try {

            if (!$id_ville || !$id_article || !$quantite) {
                throw new Exception("Tous les champs sont obligatoires.");
            }

            $this->model->processAchat(
                $id_ville,
                $id_article,
                $quantite
            );

            Flight::redirect('/achats');

        } catch (Exception $e) {

            Flight::halt(500, $e->getMessage());
        }
    }
}
