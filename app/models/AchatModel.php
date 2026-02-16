<?php

namespace app\models;

use PDO;
use Exception;

class AchatModel {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function processAchat($id_ville, $id_article, $quantite) {

        $this->db->beginTransaction();

        try {

            $restant = $this->getBesoinRestant($id_ville, $id_article);

            if ($quantite > $restant) {
                throw new Exception("Achat dépasse besoin restant.");
            }

            $pu = $this->getPU($id_article);

            if (!$pu) {
                throw new Exception("Article introuvable.");
            }

            $total_ht = $quantite * $pu;

            $taux = $this->getTauxFrais();
            $total_ttc = $total_ht * (1 + $taux / 100);

            $paiements = $this->consumeArgentFIFO($total_ttc);

            if (!$paiements) {
                throw new Exception("Pas assez d'argent disponible.");
            }

            $id_achat = $this->insertAchat(
                $id_ville,
                $taux,
                $total_ht,
                $total_ttc
            );

            $this->insertAchatLigne(
                $id_achat,
                $id_article,
                $quantite,
                $pu,
                $total_ht
            );

            $this->insertPaiements($id_achat, $paiements);

            $this->db->commit();

        } catch (Exception $e) {

            $this->db->rollBack();
            throw $e;
        }
    }


    public function getTauxFrais() {
        $stmt = $this->db->prepare(
            "SELECT value FROM settings WHERE `key`='taux_frais_achat'"
        );
        $stmt->execute();
        return (float)$stmt->fetchColumn();
    }

    private function getPU($id_article) {
        $stmt = $this->db->prepare(
            "SELECT pu FROM article WHERE id_article=?"
        );
        $stmt->execute([$id_article]);
        return (float)$stmt->fetchColumn();
    }

    public function getBesoinRestant($id_ville, $id_article) {

        $sql = "
            SELECT 
                b.quantite - IFNULL(SUM(al.quantite),0) as restant
            FROM besoin_ville b
            LEFT JOIN achat a ON a.id_ville = b.id_ville
            LEFT JOIN achat_ligne al 
                ON al.id_achat = a.id_achat 
                AND al.id_article = b.id_article
            WHERE b.id_ville = ? AND b.id_article = ?
            GROUP BY b.id_besoin
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_ville, $id_article]);
        return (float)$stmt->fetchColumn();
    }

    private function consumeArgentFIFO($montant_total) {

        $dons = $this->getArgentDonsFIFO();
        $remaining = $montant_total;
        $paiements = [];

        foreach ($dons as $don) {

            if ($remaining <= 0) break;

            if ($don['quantite'] <= 0) continue;

            $used = min($don['quantite'], $remaining);

            $paiements[] = [
                'id_don' => $don['id_don'],
                'montant' => $used
            ];

            $stmt = $this->db->prepare("
                UPDATE don 
                SET quantite = quantite - ?
                WHERE id_don = ?
            ");
            $stmt->execute([$used, $don['id_don']]);

            $remaining -= $used;
        }

        if ($remaining > 0) {
            return false;
        }

        return $paiements;
    }

    private function insertAchat($id_ville, $taux, $ht, $ttc) {

        $stmt = $this->db->prepare("
            INSERT INTO achat(id_ville, taux_frais, total_ht, total_ttc)
            VALUES(?,?,?,?)
        ");

        $stmt->execute([$id_ville, $taux, $ht, $ttc]);

        return $this->db->lastInsertId();
    }

    private function insertAchatLigne(
        $id_achat,
        $id_article,
        $quantite,
        $pu,
        $total_ht
    ) {

        $stmt = $this->db->prepare("
            INSERT INTO achat_ligne
            VALUES(?,?,?,?,?)
        ");

        $stmt->execute([
            $id_achat,
            $id_article,
            $quantite,
            $pu,
            $total_ht
        ]);
    }

    private function insertPaiements($id_achat, $paiements) {

        foreach ($paiements as $p) {

            $stmt = $this->db->prepare("
                INSERT INTO achat_paiement
                (id_achat, id_don_argent, montant_utilise)
                VALUES(?,?,?)
            ");

            $stmt->execute([
                $id_achat,
                $p['id_don'],
                $p['montant']
            ]);
        }
    }

    public function getArgentDonsFIFO() {
        return $this->db->query("
            SELECT d.*
            FROM don d
            JOIN article a ON a.id_article = d.id_article
            WHERE a.pu = 1
            ORDER BY d.date_don, d.id_don
        ")->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getVilles() {
        return $this->db->query("SELECT * FROM ville")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArticles() {
        return $this->db->query("SELECT * FROM article")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listBesoins() {
        return $this->db->query("
            SELECT v.name as ville,
                   a.name as article,
                   b.quantite,
                   b.date_saisie
            FROM besoin_ville b
            JOIN ville v ON v.id_ville = b.id_ville
            JOIN article a ON a.id_article = b.id_article
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listAchats() {
        return $this->db->query("
            SELECT a.*, v.name as ville
            FROM achat a
            JOIN ville v ON v.id_ville = a.id_ville
            ORDER BY a.id_achat DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
}
