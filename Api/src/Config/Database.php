<?php
namespace App\Config;
use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $this->conn = new PDO("mysql:host=localhost;dbname=systempo_Api_solaxgen_ppas;charset=utf8", "systempo_api_solaxgen_ppas", "SODwfQ+K[k8OiH]@");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }
    }

    public static function getInstance() {
        if (!self::$instance) { self::$instance = new Database(); }
        return self::$instance;
    }

    public function getConnection() { return $this->conn; }
}