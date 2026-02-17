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

    /**
     * Remet la base à l'état initial (données de test officielles).
     * - Vide tables "mouvements" puis "référentiels"
     * - Réinsère settings + régions/villes/types/articles + besoins + dons
     *
     * @return array résumé
     * @throws \Throwable
     */
    public function resetData(): array
    {
        try {
            $this->pdo->beginTransaction();

            // Désactiver FK pour pouvoir TRUNCATE sans se battre avec l'ordre
            $this->pdo->exec("SET FOREIGN_KEY_CHECKS=0");

            // 1) Vider tables "transactions" / dépendantes
            // (TRUNCATE remet aussi AUTO_INCREMENT)
            $this->pdo->exec("TRUNCATE TABLE achat_paiement");
            $this->pdo->exec("TRUNCATE TABLE achat_ligne");
            $this->pdo->exec("TRUNCATE TABLE achat");

            $this->pdo->exec("TRUNCATE TABLE dispatch");
            $this->pdo->exec("TRUNCATE TABLE don");
            $this->pdo->exec("TRUNCATE TABLE besoin_ville");

            // 2) Vider référentiels (si tu veux repartir exactement à zéro)
            $this->pdo->exec("TRUNCATE TABLE article");
            $this->pdo->exec("TRUNCATE TABLE type_besoin");
            $this->pdo->exec("TRUNCATE TABLE ville");
            $this->pdo->exec("TRUNCATE TABLE region");

            // 3) Settings (clé primaire string -> TRUNCATE OK)
            $this->pdo->exec("TRUNCATE TABLE settings");

            // Réactiver FK
            $this->pdo->exec("SET FOREIGN_KEY_CHECKS=1");

            // =========================
            // INSERT DONNEES ORIGINES
            // =========================

            // REGIONS
            $stmt = $this->pdo->prepare("INSERT INTO region (name) VALUES (?)");
            foreach (['Analamanga', 'Atsinanana', 'Haute Matsiatra'] as $name) {
                $stmt->execute([$name]);
            }

            // VILLES (id_region 1..)
            $stmt = $this->pdo->prepare("INSERT INTO ville (id_region, name, nb_sinistres) VALUES (?, ?, ?)");
            $villes = [
                [1, 'Antananarivo', 1200],
                [1, 'Ambohidratrimo', 350],
                [2, 'Toamasina', 800],
                [3, 'Fianarantsoa', 500],
            ];
            foreach ($villes as $v) {
                $stmt->execute($v);
            }

            // TYPES
            $stmt = $this->pdo->prepare("INSERT INTO type_besoin (name) VALUES (?)");
            foreach (['NATURE', 'MATERIAUX', 'ARGENT'] as $t) {
                $stmt->execute([$t]);
            }

            // ARTICLES (PU FIXE)
            // On récupère les id_type depuis la DB
            $typeId = [];
            $rows = $this->pdo->query("SELECT id_type, name FROM type_besoin")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $typeId[$r['name']] = (int)$r['id_type'];
            }

            $stmt = $this->pdo->prepare("INSERT INTO article (id_type, name, pu) VALUES (?, ?, ?)");
            $articles = [
                [$typeId['NATURE'], 'Riz (kg)', 3500.00],
                [$typeId['NATURE'], 'Huile (L)', 9000.00],
                [$typeId['MATERIAUX'], 'Tôle (unité)', 25000.00],
                [$typeId['MATERIAUX'], 'Clou (kg)', 12000.00],
                [$typeId['ARGENT'], 'Argent', 1.00],
            ];
            foreach ($articles as $a) {
                $stmt->execute($a);
            }

            // Récupérer les id_ville et id_article par name pour les inserts suivants
            $villeId = [];
            $rows = $this->pdo->query("SELECT id_ville, name FROM ville")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $villeId[$r['name']] = (int)$r['id_ville'];
            }

            $articleId = [];
            $rows = $this->pdo->query("SELECT id_article, name FROM article")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $articleId[$r['name']] = (int)$r['id_article'];
            }

            // BESOINS (par ville)
            $stmt = $this->pdo->prepare("
                INSERT INTO besoin_ville (id_ville, id_article, quantite, date_saisie, status)
                VALUES (?, ?, ?, ?, 'non_satisfait')
            ");

            $besoins = [
                // Antananarivo
                [$villeId['Antananarivo'], $articleId['Riz (kg)'], 800, '2026-02-16 13:10:00'],
                [$villeId['Antananarivo'], $articleId['Huile (L)'], 120, '2026-02-16 13:11:00'],
                [$villeId['Antananarivo'], $articleId['Argent'], 1500000, '2026-02-16 13:12:00'],

                // Toamasina
                [$villeId['Toamasina'], $articleId['Tôle (unité)'], 60, '2026-02-16 13:13:00'],
                [$villeId['Toamasina'], $articleId['Clou (kg)'], 25, '2026-02-16 13:14:00'],
                [$villeId['Toamasina'], $articleId['Argent'], 2200000, '2026-02-16 13:15:00'],

                // Fianarantsoa
                [$villeId['Fianarantsoa'], $articleId['Riz (kg)'], 300, '2026-02-16 13:16:00'],
            ];

            foreach ($besoins as $b) {
                $stmt->execute($b);
            }

            // DONS
            $stmt = $this->pdo->prepare("
                INSERT INTO don (id_article, quantite, date_don, source, statut)
                VALUES (?, ?, ?, ?, 'NON_DISPATCHE')
            ");

            $dons = [
                [$articleId['Riz (kg)'], 500, '2026-02-16 14:00:00', 'Association Aina'],
                [$articleId['Riz (kg)'], 400, '2026-02-16 14:30:00', 'Donateur privé'],
                [$articleId['Tôle (unité)'], 40, '2026-02-16 15:00:00', 'ONG Build'],
                [$articleId['Argent'], 1000000, '2026-02-16 15:10:00', 'Entreprise X'],
            ];
            foreach ($dons as $d) {
                $stmt->execute($d);
            }

            // SETTINGS
            $stmt = $this->pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?)");
            $stmt->execute(['taux_frais_achat', '10']);

            // $this->pdo->commit();

            return [
                'ok' => true,
                'message' => 'Base réinitialisée avec succès.',
                'counts' => [
                    'regions' => 3,
                    'villes' => 4,
                    'types' => 3,
                    'articles' => 5,
                    'besoins' => 7,
                    'dons' => 4,
                    'settings' => 1,
                ],
            ];
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
