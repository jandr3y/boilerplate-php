<?php
namespace App\Services\Db;

class Db {

  public $db;
  public $table;
  public $model;

  public function __construct($db, $table, $model){
    $this->db = $db;
    $this->table = $table;
    $this->model = $model;
  }

  public function findOne($where){
    $smtp = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$where}");
    $smtp->execute();

    $model = "\\App\\Models\\";
    $model .= $this->model;
    return $smtp->fetchObject($model);
  }

}