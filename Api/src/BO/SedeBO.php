<?php
namespace App\BO;
use App\DAO\SedeDAO;
use App\TO\SedeTO;

class SedeBO {
    private $dao;

    public function __construct() {
        $this->dao = new SedeDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(SedeTO $sedeTO) { return $this->dao->create($sedeTO); }
    
    public function update(SedeTO $sedeTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($sedeTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}