<?php
namespace App\BO;
use App\DAO\PlantillaDocumentoDAO;
use App\TO\PlantillaDocumentoTO;

class PlantillaDocumentoBO {
    private $dao;

    public function __construct() {
        $this->dao = new PlantillaDocumentoDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(PlantillaDocumentoTO $plantillaTO) { return $this->dao->create($plantillaTO); }
    
    public function update(PlantillaDocumentoTO $plantillaTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($plantillaTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}