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

  public function find($where = null, $limit = null){
    $sql = "select * from {$this->table} ";

    if(isset($where))
      $sql .= "where {$where} ";

    if(isset($limit))
      $sql .= "limit {$limit}";


      var_dump($sql);
      $smtp = $this->db->prepare($sql);
      $smtp->execute();

      return $smtp->fetchAll();
  }

}