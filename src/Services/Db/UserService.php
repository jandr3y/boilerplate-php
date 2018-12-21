<?php
namespace App\Services\Db;

class UserService {

  private $db;
  private $table = 'users';

  public function __construct($db){
    $this->db = $db;

  }

  public function findOne($where){
    $smtp = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$where}");

    $smtp->execute();
    return $smtp->fetchObject('\App\Models\User');
  }


}