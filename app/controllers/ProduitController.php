<?php

namespace app\controllers;

use flight\Engine;
use app\models\Produits;
use Flight;

class ProduitController
{

    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    public function listProduits()
    {
        $produit = new Produits(Flight::db());
        $produits = $produit->listProduits();
        $this->app->render('produits', [
            'produits' => $produits
        ]);
    }
    public function listProduit($id)
    {
        $produit = new Produits(Flight::db());
        $produits = $produit->getProduitByid($id);
        $data= [
            'id' => $produits['id'],
            'name' => $produits['nom'],
            'description' => $produits['description'],
            'prix' => $produits['prix'],
            'image' => $produits['image']
        ];
        Flight::render('produit',$data);
    }
}
