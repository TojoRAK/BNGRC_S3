<?php

namespace app\controllers;


use app\models\ResetModel;
use Flight;
use flight\Engine;

class ResetController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    function resetData(){
        $model = new ResetModel(Flight::db());
        $model->resetData();
        Flight::redirect("/");
    }

}