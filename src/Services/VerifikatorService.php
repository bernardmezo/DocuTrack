<?php
namespace App\Services;

use App\Models\VerifikatorModel;
use Exception;

class VerifikatorService {
    private $model;

    public function __construct($db) {
        $this->model = new VerifikatorModel($db);
    }

    // Explicit proxy methods to resolve "Method not found" errors
    public function getDashboardStats() {
        return $this->model->getDashboardStats();
    }

    public function getDashboardKAK() {
        return $this->model->getDashboardKAK();
    }

    public function getListJurusan() {
        return $this->model->getListJurusan();
    }
    
    public function getRiwayat() {
        return $this->model->getRiwayat();
    }

    // Fallback for any other methods not explicitly defined
    public function __call($name, $arguments) {
        if (method_exists($this->model, $name)) {
            return call_user_func_array([$this->model, $name], $arguments);
        }
        throw new Exception("Method {$name} not found in VerifikatorModel or VerifikatorService");
    }
}