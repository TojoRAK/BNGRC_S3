<?php

namespace app\controllers;

use app\models\BesoinModel;
use Exception;
use Flight;
use flight\Engine;

class BesoinController
{
    protected Engine $app;

    public function __construct()
    {
        $this->app = Flight::app();
    }


    public function index()
    {
        $model = new BesoinModel(Flight::db());

        $villes   = $model->getVilles();
        $articles = $model->getArticles();
        $besoins  = $model->listBesoins();

        Flight::render('besoins', [
            'villes' => $villes,
            'articles' => $articles,
            'besoins' => $besoins
        ]);
    }


    public function store()
    {
        $ville_id   = $_POST['ville_id'] ?? null;
        $article_id = $_POST['article_id'] ?? null;
        $quantite   = $_POST['quantite'] ?? null;

        try {

            if (!$ville_id || !$article_id || !$quantite) {
                throw new Exception("Tous les champs sont obligatoires.");
            }

            $model = new BesoinModel(Flight::db());

            // si pu = 1 alors on considère que c’est un montant
            $stmt = Flight::db()->prepare("SELECT pu FROM article WHERE id_article = ?");
            $stmt->execute([$article_id]);
            $article = $stmt->fetch();

            if (!$article) {
                throw new Exception("Article introuvable.");
            }

            if ($article['pu'] == 1) {
                // argent => quantite = montant saisi
                $quantite = $quantite;
            }

            $model->createBesoin($ville_id, $article_id, $quantite);

            Flight::redirect('/besoins');

        } catch (Exception $e) {
            Flight::halt(500, $e->getMessage());
        }
    }
}
