<?php
namespace App\BO;
use App\DAO\PlantillaSeccionDAO;
use App\TO\PlantillaSeccionTO;

class PlantillaSeccionBO {
    private $dao;

    public function __construct() {
        $this->dao = new PlantillaSeccionDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(PlantillaSeccionTO $seccionTO) { return $this->dao->create($seccionTO); }
    
    public function update(PlantillaSeccionTO $seccionTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($seccionTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}