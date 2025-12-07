<?php
namespace App\Services;

use App\Models\PpkModel;
use Exception;

class PpkService {
    private $model;

    public function __construct($db) {
        $this->model = new PpkModel($db);
    }

    // Explicit proxy methods to resolve "Method not found" errors
    public function getDashboardStats() {
        return $this->model->getDashboardStats();
    }

    public function getDashboardKAK() {
        return $this->model->getDashboardKAK();
    }

    public function getListJurusanDistinct() {
        return $this->model->getListJurusanDistinct();
    }

    public function getRiwayat() {
        return $this->model->getRiwayat();
    }

    // Fallback for any other methods not explicitly defined
    public function __call($name, $arguments) {
        if (method_exists($this->model, $name)) {
            return call_user_func_array([$this->model, $name], $arguments);
        }
        throw new Exception("Method {$name} not found in PpkModel or PpkService");
    }
}