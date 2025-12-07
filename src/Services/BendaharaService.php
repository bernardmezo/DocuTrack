<?php
namespace App\Services;

use App\Models\BendaharaModel;
use Exception;

class BendaharaService {
    private $model;

    public function __construct($db) {
        $this->model = new BendaharaModel($db);
    }

    public function __call($name, $arguments) {
        if (method_exists($this->model, $name)) {
            return call_user_func_array([$this->model, $name], $arguments);
        }
        throw new Exception("Method {$name} not found in BendaharaModel");
    }
}