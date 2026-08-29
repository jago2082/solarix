<?php
namespace App\BO;
use App\DAO\PlanPpaDAO;
use App\TO\PlanPpaTO;

class PlanPpaBO {
    private $dao;

    public function __construct() {
        $this->dao = new PlanPpaDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(PlanPpaTO $planTO) { return $this->dao->create($planTO); }
    
    public function update(PlanPpaTO $planTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($planTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}