<?php
namespace App\BO;
use App\DAO\VisitaDAO;
use App\TO\VisitaTO;

class VisitaBO {
    private $dao;

    public function __construct() {
        $this->dao = new VisitaDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(VisitaTO $visitaTO) { return $this->dao->create($visitaTO); }
    
    public function update(VisitaTO $visitaTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($visitaTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}