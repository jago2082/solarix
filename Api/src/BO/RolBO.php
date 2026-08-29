<?php
namespace App\BO;
use App\DAO\RolDAO;
use App\TO\RolTO;

class RolBO {
    private $dao;

    public function __construct() {
        $this->dao = new RolDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(RolTO $rolTO) { return $this->dao->create($rolTO); }
    
    public function update(RolTO $rolTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($rolTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}