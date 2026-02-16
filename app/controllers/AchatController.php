<?php

namespace app\controllers;

use app\models\AchatModel;
use Exception;
use Flight;

class AchatController
{
    private $model;

    public function __construct()
    {
        $this->model = new AchatModel(Flight::db());
    }

    public function index()
    {
        $totalArgent = $this->model->getTotalArgentDons();
        $besoins = $this->model->getBesoinsSummary();
        $achats = $this->model->getAchatsHistorique();
        $villes = $this->model->getVilles();
        $articles = $this->model->getArticles();

        Flight::render('achats', [
            'totalArgent' => $totalArgent,
            'besoins' => $besoins,
            'achats' => $achats,
            'villes' => $villes,
            'articles' => $articles
        ]);
    }

    public function store()
    {
        $id_ville = $_POST['ville_id'] ?? null;
        $id_article = $_POST['article_id'] ?? null;
        $quantite = isset($_POST['quantite']) ? (float)$_POST['quantite'] : 0;

        try {
            if (!$id_ville || !$id_article || !$quantite) {
                throw new Exception("Tous les champs sont obligatoires.");
            }

            $db = Flight::db();
            $db->beginTransaction();

            $restant = $this->model->getBesoinRestant($id_ville, $id_article);
            if ($quantite > $restant) {
                throw new Exception("Achat dépasse le besoin restant.");
            }

            
            $stmt = $db->prepare("SELECT pu FROM article WHERE id_article=?");
            $stmt->execute([$id_article]);
            $pu = (float)$stmt->fetchColumn();
            if (!$pu) throw new Exception("Article introuvable.");

            $total_ht = $quantite * $pu;

            
            $taux = $this->model->getTauxFrais();
            $total_ttc = $total_ht * (1 + $taux / 100);

           
            $dons = $this->model->getArgentDonsFIFO();
            $remaining = $total_ttc;
            $paiements = [];

            foreach ($dons as $don) {
                if ($remaining <= 0) break;
                $available = $don['quantite'];
                if ($available <= 0) continue;
                $used = min($available, $remaining);
                $paiements[] = ['id_don' => $don['id_don'], 'montant' => $used];
                $remaining -= $used;
            }

            if ($remaining > 0) throw new Exception("Pas assez d'argent disponible.");

            $stmt = $db->prepare("INSERT INTO achat(id_ville, taux_frais, total_ht, total_ttc) VALUES(?,?,?,?)");
            $stmt->execute([$id_ville, $taux, $total_ht, $total_ttc]);
            $id_achat = $db->lastInsertId();

            
            $stmt = $db->prepare("INSERT INTO achat_ligne(id_achat,id_article,quantite,pu,montant_ht) VALUES(?,?,?,?,?)");
            $stmt->execute([$id_achat, $id_article, $quantite, $pu, $total_ht]);

            
            foreach ($paiements as $p) {
                $stmt = $db->prepare("INSERT INTO achat_paiement(id_achat,id_don_argent,montant_utilise) VALUES(?,?,?)");
                $stmt->execute([$id_achat, $p['id_don'], $p['montant']]);

                $stmt = $db->prepare("UPDATE don SET quantite = quantite - ? WHERE id_don=?");
                $stmt->execute([$p['montant'], $p['id_don']]);
            }

            $db->commit();
            Flight::redirect('/achats');
        } catch (Exception $e) {
            $db->rollBack();
            Flight::halt(500, $e->getMessage());
        }
    }
}
