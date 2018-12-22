<?php
namespace App\Services\Db;

use App\Services\Db\Db;
use App\Models\User;

class UserService extends Db {

  public function __construct($db){
    parent::__construct($db, 'users', 'User');
  }

  public function create(User $u){
    $sql = "insert into users 
                (username, password, name ) 
              VALUES 
                (:username, :password, :name) ";
    
    $smtp = $this->db->prepare($sql);

    $smtp->bindParam(':username', $u->username);
    $smtp->bindParam(':password', $u->password);
    $smtp->bindParam(':name', $u->name);

    return $smtp->execute();
  }


}