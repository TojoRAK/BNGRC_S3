<?php

namespace app\models;

use PDO;

class AchatModel
{

    private $db;

    public function __construct($db)
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
            JOIN article a ON a.id_article=d.id_article 
            WHERE a.pu = 1
        ");
        return (float)$stmt->fetchColumn();
    }

    
    public function getAchatsSummary(): array
    {
        $sql = "
            SELECT 
                b.id_besoin,
                v.id_ville,
                v.name AS ville,
                a.id_article,
                a.name AS article,
                b.quantite AS besoin_initial,
                COALESCE(SUM(al.quantite),0) AS quantite_achetee,
                b.quantite - COALESCE(SUM(al.quantite),0) AS restant
            FROM besoin_ville b
            JOIN ville v ON v.id_ville = b.id_ville
            JOIN article a ON a.id_article = b.id_article
            LEFT JOIN achat_ligne al ON al.id_article = b.id_article
            LEFT JOIN achat ach ON ach.id_achat = al.id_achat AND ach.id_ville = b.id_ville
            GROUP BY b.id_besoin
            ORDER BY v.name, a.name
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArgentDonsFIFO(): array
    {
        $stmt = $this->db->query("
            SELECT d.*
            FROM don d
            JOIN article a ON a.id_article = d.id_article
            WHERE a.pu = 1
            ORDER BY d.date_don, d.id_don
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBesoinRestant($id_ville, $id_article): float
    {
        $stmt = $this->db->prepare("
            SELECT quantite - COALESCE(SUM(al.quantite),0)
            FROM besoin_ville b
            LEFT JOIN achat_ligne al 
                ON al.id_article = b.id_article
            LEFT JOIN achat ach 
                ON ach.id_achat = al.id_achat AND ach.id_ville = b.id_ville
            WHERE b.id_ville = ? AND b.id_article = ?
            GROUP BY b.id_besoin
        ");
        $stmt->execute([$id_ville, $id_article]);
        return (float)$stmt->fetchColumn();
    }

    public function getHistoriqueAchats(): array
    {
        $sql = "
        SELECT 
            ach.id_achat,
            v.name AS ville,
            ach.total_ht,
            ach.total_ttc,
            ach.taux_frais,
            ach.date_achat,
            al.id_article,
            a.name AS article,
            al.quantite AS quantite_achetee
        FROM achat ach
        JOIN achat_ligne al ON al.id_achat = ach.id_achat
        JOIN ville v ON v.id_ville = ach.id_ville
        JOIN article a ON a.id_article = al.id_article
        ORDER BY ach.date_achat DESC, ach.id_achat DESC
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
