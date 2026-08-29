<?php
namespace App\BO;
use App\DAO\SesionDAO;
use App\TO\SesionTO;

class SesionBO {
    private $dao;

    public function __construct() {
        $this->dao = new SesionDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(SesionTO $sesionTO) { return $this->dao->create($sesionTO); }
    
    public function update(SesionTO $sesionTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($sesionTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}