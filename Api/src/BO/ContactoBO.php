<?php
namespace App\BO;
use App\DAO\ContactoDAO;
use App\TO\ContactoTO;

class ContactoBO {
    private $dao;

    public function __construct() {
        $this->dao = new ContactoDAO();
    }

    public function getAll() { return $this->dao->getAll(); }
    public function getById($id) { return $this->dao->getById($id); }
    public function create(ContactoTO $contactoTO) { return $this->dao->create($contactoTO); }
    
    public function update(ContactoTO $contactoTO, $id) {
        if (empty($id)) return ['status' => 'error', 'message' => 'ID requerido'];
        return $this->dao->update($contactoTO, $id);
    }

    public function delete($id) { return $this->dao->delete($id); }
}