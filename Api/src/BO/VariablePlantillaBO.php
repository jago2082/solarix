<?php
namespace App\BO;
use App\DAO\VariablePlantillaDAO;
use App\TO\VariablePlantillaTO;

class VariablePlantillaBO {
    private $dao;

    public function __construct() {
        $this->dao = new VariablePlantillaDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(VariablePlantillaTO $variableTO) { return $this->dao->create($variableTO); }
    
    public function update(VariablePlantillaTO $variableTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($variableTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}