<?php
namespace App\BO;
use App\DAO\PlantillaVariableDAO;
use App\TO\PlantillaVariableTO;

class PlantillaVariableBO {
    private $dao;

    public function __construct() {
        $this->dao = new PlantillaVariableDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(PlantillaVariableTO $variableTO) { return $this->dao->create($variableTO); }
    
    public function update(PlantillaVariableTO $variableTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($variableTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}