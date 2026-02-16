<?php
namespace app\models;
class Produits {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    public function listProduits(){
        $produits=$this->db->query("Select * from produit");
        return $produits->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getProduitByid($id){
        $query="Select * from produit where id=". $id;
        $produit=$this->db->query($query);
        return $produit->fetch();
    }
}


?>