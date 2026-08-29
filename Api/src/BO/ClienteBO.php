<?php
namespace App\BO;
use App\DAO\ClienteDAO;
use App\TO\ClienteTO;

class ClienteBO {
    private $dao;

    public function __construct() {
        $this->dao = new ClienteDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(ClienteTO $clienteTO) { return $this->dao->create($clienteTO); }
    
    public function update(ClienteTO $clienteTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($clienteTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}