<?php
namespace App\BO;
use App\DAO\ProyectoDAO;
use App\TO\ProyectoTO;

class ProyectoBO {
    private $dao;

    public function __construct() {
        $this->dao = new ProyectoDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(ProyectoTO $proyectoTO) { return $this->dao->create($proyectoTO); }
    
    public function update(ProyectoTO $proyectoTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($proyectoTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}