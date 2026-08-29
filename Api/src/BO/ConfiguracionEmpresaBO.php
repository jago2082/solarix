<?php
namespace App\BO;
use App\DAO\ConfiguracionEmpresaDAO;
use App\TO\ConfiguracionEmpresaTO;

class ConfiguracionEmpresaBO {
    private $dao;

    public function __construct() {
        $this->dao = new ConfiguracionEmpresaDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(ConfiguracionEmpresaTO $configTO) { return $this->dao->create($configTO); }
    
    public function update(ConfiguracionEmpresaTO $configTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($configTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}