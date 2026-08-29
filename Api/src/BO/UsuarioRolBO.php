<?php
namespace App\BO;
use App\DAO\UsuarioRolDAO;
use App\TO\UsuarioRolTO;

class UsuarioRolBO {
    private $dao;

    public function __construct() {
        $this->dao = new UsuarioRolDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(UsuarioRolTO $usuarioRolTO) { return $this->dao->create($usuarioRolTO); }
    
    public function update(UsuarioRolTO $usuarioRolTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($usuarioRolTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}