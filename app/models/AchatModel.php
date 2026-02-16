<?php

namespace app\models;

use PDO;

class AchatModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getTauxFrais(): float
    {
        $stmt = $this->db->prepare("SELECT value FROM settings WHERE `key`='taux_frais_achat'");
        $stmt->execute();
        return (float)$stmt->fetchColumn();
    }

    public function getTotalArgentDons(): float
    {
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(d.quantite),0) 
            FROM don d 
            JOIN article art ON art.id_article=d.id_article 
            WHERE art.pu = 1
        ");
        return (float)$stmt->fetchColumn();
    }

    public function getVilles(): array
    {
        return $this->db->query("SELECT id_ville, name FROM ville ORDER BY name")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArticles(): array
    {
        return $this->db->query("SELECT id_article, name, pu FROM article ORDER BY name")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBesoinRestant($id_ville, $id_article): float
    {
        $stmt = $this->db->prepare("
            SELECT SUM(b.quantite) - COALESCE(SUM(al.quantite),0) as restant
            FROM besoin_ville b
            LEFT JOIN achat ach ON ach.id_ville = b.id_ville
            LEFT JOIN achat_ligne al ON al.id_achat = ach.id_achat AND al.id_article = b.id_article
            WHERE b.id_ville = ? AND b.id_article = ?
        ");
        $stmt->execute([$id_ville, $id_article]);
        return (float)$stmt->fetchColumn();
    }

    public function getArgentDonsFIFO(): array
    {
        return $this->db->query("
            SELECT d.*
            FROM don d
            JOIN article art ON art.id_article = d.id_article
            WHERE art.pu = 1
            ORDER BY d.date_don, d.id_don
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAchatsHistorique(): array
    {
        $sql = "
            SELECT 
                ach.id_achat,
                v.name AS ville,
                art.name AS article,
                al.quantite AS quantite_achetee,
                ach.total_ht,
                ach.total_ttc,
                ach.taux_frais,
                ach.date_achat
            FROM achat ach
            JOIN achat_ligne al ON al.id_achat = ach.id_achat
            JOIN ville v ON v.id_ville = ach.id_ville
            JOIN article art ON art.id_article = al.id_article
            ORDER BY ach.date_achat DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBesoinsSummary(): array
    {
        $sql = "
            SELECT 
                b.id_ville,
                b.id_article,
                v.name AS ville,
                art.name AS article,
                SUM(b.quantite) AS besoin_initial,
                COALESCE(SUM(al.quantite),0) AS quantite_achetee,
                SUM(b.quantite) - COALESCE(SUM(al.quantite),0) AS restant
            FROM besoin_ville b
            LEFT JOIN achat ach ON ach.id_ville = b.id_ville
            LEFT JOIN achat_ligne al ON al.id_achat = ach.id_achat AND al.id_article = b.id_article
            JOIN ville v ON v.id_ville = b.id_ville
            JOIN article art ON art.id_article = b.id_article
            GROUP BY b.id_ville, b.id_article
            ORDER BY v.name, art.name
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
