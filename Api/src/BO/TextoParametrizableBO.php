<?php
namespace App\BO;
use App\DAO\TextoParametrizableDAO;
use App\TO\TextoParametrizableTO;

class TextoParametrizableBO {
    private $dao;

    public function __construct() {
        $this->dao = new TextoParametrizableDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(TextoParametrizableTO $textoTO) { return $this->dao->create($textoTO); }
    
    public function update(TextoParametrizableTO $textoTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($textoTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}