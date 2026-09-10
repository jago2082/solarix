<?php
namespace App\BO;
require_once __DIR__ . '/../DAO/DtlleVariablePlantillaDAO.php';
require_once __DIR__ . '/../TO/DtlleVariablePlantillaTO.php';
use App\DAO\DtlleVariablePlantillaDAO;
use App\TO\DtlleVariablePlantillaTO;

class DtlleVariablePlantillaBO {
    private $dao;

    public function __construct() {
        $this->dao = new DtlleVariablePlantillaDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function getByVplCont($vplCont) { return $this->dao->getByVplCont($vplCont); }
    public function create(DtlleVariablePlantillaTO $detalleTO) { return $this->dao->create($detalleTO); }

    public function update(DtlleVariablePlantillaTO $detalleTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($detalleTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
    public function deleteByVplCont($vplCont) { return $this->dao->deleteByVplCont($vplCont); }
}
