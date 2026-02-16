<?php

namespace app\controllers;

use app\models\DonModel;
use Exception;
use Flight;
use flight\Engine;

class DonController
{
    protected Engine $app;

    public function __construct()
    {
        $this->app = Flight::app();
    }

 
    public function index()
    {
        $model = new DonModel(Flight::db());

        $articles = $model->getArticles();
        $dons     = $model->listDons();

        Flight::render('dons', [
            'articles' => $articles,
            'dons' => $dons
        ]);
    }

 
    public function store()
    {
        $article_id = $_POST['article_id'] ?? null;
        $quantite   = $_POST['quantite'] ?? null;
        $date_don   = $_POST['date_don'] ?? null;
        $source     = $_POST['source'] ?? null;

        try {

            if (!$article_id || !$quantite || !$date_don) {
                throw new Exception("Tous les champs obligatoires doivent être remplis.");
            }

            $model = new DonModel(Flight::db());

            //metier argent
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

            $model->createDon($article_id, $quantite, $date_don, $source);

            Flight::redirect('/dons');

        } catch (Exception $e) {
            Flight::halt(500, $e->getMessage());
        }
    }
}
