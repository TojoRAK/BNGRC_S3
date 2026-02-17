<?php

namespace app\models;

use PDO;

class ResetModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function resetData(): array
    {
        try {
            $this->pdo->beginTransaction();
            $this->pdo->exec("SET FOREIGN_KEY_CHECKS=0");

            // 1) Vider mouvements
            $this->pdo->exec("DELETE FROM achat_paiement");
            $this->pdo->exec("DELETE FROM achat_ligne");
            $this->pdo->exec("DELETE FROM achat");
            $this->pdo->exec("DELETE FROM dispatch");
            $this->pdo->exec("DELETE FROM besoin_ville");
            $this->pdo->exec("DELETE FROM don");

            // 2) Vider référentiels concernés (seed complet)
            $this->pdo->exec("DELETE FROM article");
            $this->pdo->exec("DELETE FROM ville");
            $this->pdo->exec("DELETE FROM region");
            $this->pdo->exec("DELETE FROM type_besoin");
            $this->pdo->exec("DELETE FROM settings");

            $this->pdo->exec("SET FOREIGN_KEY_CHECKS=1");

            // =========================
            // SEED COMPLET
            // =========================

            // REGIONS (si tu veux une seule region "par défaut", mets juste 1)
            $stmt = $this->pdo->prepare("INSERT INTO region (name) VALUES (?)");
            foreach (['Analamanga', 'Atsinanana', 'Haute Matsiatra'] as $r) {
                $stmt->execute([$r]);
            }

            // TYPE_BESOIN (ordre important)
            $stmt = $this->pdo->prepare("INSERT INTO type_besoin (name) VALUES (?)");
            foreach (['NATURE', 'MATERIAUX', 'ARGENT'] as $t) {
                $stmt->execute([$t]);
            }

            // VILLES (on rattache à une région existante par nom, pas id fixe)
            // Ici je mets toutes les villes que tu utilises dans tes besoins
            $sqlVille = "
            INSERT INTO ville (id_region, name, nb_sinistres)
            VALUES (
                (SELECT id_region FROM region WHERE name = ? LIMIT 1),
                ?,
                ?
            )
        ";
            $stmtVille = $this->pdo->prepare($sqlVille);

            // Choix simple : mettre toutes ces villes en Analamanga (ou adapte)
            $regionDefault = 'Atsinanana';

            $villes = [
                ['Toamasina', 0],
                ['Mananjary', 0],
                ['Farafangana', 0],
                ['Nosy Be', 0],
                ['Morondava', 0],
            ];

            foreach ($villes as [$name, $nb]) {
                $stmtVille->execute([$regionDefault, $name, $nb]);
            }

            // ARTICLES (id_type par nom => robuste)
            $sqlArt = "
            INSERT INTO article (id_type, name, pu)
            VALUES (
                (SELECT id_type FROM type_besoin WHERE name=? LIMIT 1),
                ?,
                ?
            )
        ";
            $stmtArt = $this->pdo->prepare($sqlArt);

            $articles = [
                ['NATURE', 'Riz (kg)', 3000.00],
                ['NATURE', 'Eau (L)', 1000.00],
                ['NATURE', 'Huile (L)', 6000.00],
                ['NATURE', 'Haricots', 4000.00],

                ['MATERIAUX', 'Tôle', 25000.00],
                ['MATERIAUX', 'Bâche', 15000.00],
                ['MATERIAUX', 'Clous (kg)', 8000.00],
                ['MATERIAUX', 'Bois', 10000.00],
                ['MATERIAUX', 'groupe', 6750000.00],

                ['ARGENT', 'Argent', 1.00],
            ];
            foreach ($articles as [$typeName, $name, $pu]) {
                $stmtArt->execute([$typeName, $name, $pu]);
            }

            // SETTINGS
            $stmtSet = $this->pdo->prepare("INSERT INTO settings (`key`,`value`) VALUES (?,?)");
            $stmtSet->execute(['taux_frais_achat', '10']);

            // =========================
            // BESOINS (id_besoin = ordre)
            // =========================
            $sqlBesoin = "
            INSERT INTO besoin_ville (id_besoin, id_ville, id_article, quantite_initiale, quantite, date_saisie, status)
            VALUES (
                ?,
                (SELECT id_ville FROM ville WHERE name=? LIMIT 1),
                (SELECT id_article FROM article WHERE name=? LIMIT 1),
                ?, ?, ?, 'non_satisfait'
            )
        ";
            $stmtBesoin = $this->pdo->prepare($sqlBesoin);

            $besoins = [
                [17, 'Toamasina', 'Riz (kg)', 800, '2026-02-16 00:00:00'],
                [4, 'Toamasina', 'Eau (L)', 1500, '2026-02-15 00:00:00'],
                [23, 'Toamasina', 'Tôle', 120, '2026-02-16 00:00:00'],
                [1, 'Toamasina', 'Bâche', 200, '2026-02-15 00:00:00'],
                [12, 'Toamasina', 'Argent', 12000000, '2026-02-16 00:00:00'],
                [16, 'Toamasina', 'groupe', 3, '2026-02-15 00:00:00'],

                [9, 'Mananjary', 'Riz (kg)', 500, '2026-02-15 00:00:00'],
                [25, 'Mananjary', 'Huile (L)', 120, '2026-02-16 00:00:00'],
                [6, 'Mananjary', 'Tôle', 80, '2026-02-15 00:00:00'],
                [19, 'Mananjary', 'Clous (kg)', 60, '2026-02-16 00:00:00'],
                [3, 'Mananjary', 'Argent', 6000000, '2026-02-15 00:00:00'],

                [21, 'Farafangana', 'Riz (kg)', 600, '2026-02-16 00:00:00'],
                [14, 'Farafangana', 'Eau (L)', 1000, '2026-02-15 00:00:00'],
                [8, 'Farafangana', 'Bâche', 150, '2026-02-16 00:00:00'],
                [26, 'Farafangana', 'Bois', 100, '2026-02-15 00:00:00'],
                [10, 'Farafangana', 'Argent', 8000000, '2026-02-16 00:00:00'],

                [5, 'Nosy Be', 'Riz (kg)', 300, '2026-02-15 00:00:00'],
                [18, 'Nosy Be', 'Haricots', 200, '2026-02-16 00:00:00'],
                [2, 'Nosy Be', 'Tôle', 40, '2026-02-15 00:00:00'],
                [24, 'Nosy Be', 'Clous (kg)', 30, '2026-02-16 00:00:00'],
                [7, 'Nosy Be', 'Argent', 4000000, '2026-02-15 00:00:00'],

                [11, 'Morondava', 'Riz (kg)', 700, '2026-02-16 00:00:00'],
                [20, 'Morondava', 'Eau (L)', 1200, '2026-02-15 00:00:00'],
                [15, 'Morondava', 'Bâche', 180, '2026-02-16 00:00:00'],
                [22, 'Morondava', 'Bois', 150, '2026-02-15 00:00:00'],
                [13, 'Morondava', 'Argent', 10000000, '2026-02-16 00:00:00'],
            ];

            foreach ($besoins as [$id, $ville, $art, $qte, $date]) {
                $stmtBesoin->execute([$id, $ville, $art, $qte, $qte, $date]);
            }

            // Recaler auto_increment besoins
            $mxB = (int)$this->pdo->query("SELECT COALESCE(MAX(id_besoin),0)+1 FROM besoin_ville")->fetchColumn();
            $this->pdo->exec("ALTER TABLE besoin_ville AUTO_INCREMENT = {$mxB}");

            // =========================
            // DONS
            // =========================
            $sqlDon = "
            INSERT INTO don (id_article, quantite_initiale, quantite, date_don, source, statut)
            VALUES (
                (SELECT id_article FROM article WHERE name=? LIMIT 1),
                ?, ?, ?, ?, 'NON_DISPATCHE'
            )
        ";
            $stmtDon = $this->pdo->prepare($sqlDon);

            $dons = [
                ['Argent', 5000000, '2026-02-16 00:00:00'],
                ['Argent', 3000000, '2026-02-16 00:00:00'],
                ['Argent', 4000000, '2026-02-17 00:00:00'],
                ['Argent', 1500000, '2026-02-17 00:00:00'],
                ['Argent', 6000000, '2026-02-17 00:00:00'],
                ['Riz (kg)', 400, '2026-02-16 00:00:00'],
                ['Eau (L)', 600, '2026-02-16 00:00:00'],
                ['Tôle', 50, '2026-02-17 00:00:00'],
                ['Bâche', 70, '2026-02-17 00:00:00'],
                ['Haricots', 100, '2026-02-17 00:00:00'],
                ['Riz (kg)', 2000, '2026-02-18 00:00:00'],
                ['Tôle', 300, '2026-02-18 00:00:00'],
                ['Eau (L)', 5000, '2026-02-18 00:00:00'],
                ['Argent', 20000000, '2026-02-19 00:00:00'],
                ['Bâche', 500, '2026-02-19 00:00:00'],
                ['Haricots', 88, '2026-02-17 00:00:00'],
            ];

            foreach ($dons as [$art, $qte, $date]) {
                $stmtDon->execute([$art, $qte, $qte, $date, 'Donateur']);
            }

            // $this->pdo->commit();

            return ['ok' => true, 'message' => 'Seed complet OK'];
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }
}
