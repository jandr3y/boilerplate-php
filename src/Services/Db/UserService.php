<?php
namespace App\Services\Db;

use App\Services\Db\Db;
use App\Models\User;

class UserService extends Db {

  public function __construct($db = null, $table = "", $source = "", $hidden = null)
  {
    parent::__construct($db, $table, $source, $hidden);
  }

  public function create(User $u){
    $sql = "insert into users 
                (username, password, name ) 
              VALUES 
                (:username, :password, :name) ";
    
    $smtp = $this->db->prepare($sql);

    $smtp->bindParam(':username', $u->getUsername());
    $smtp->bindParam(':password', $u->getPassword());
    $smtp->bindParam(':name', $u->getRealname());

    return $smtp->execute();
  }

  public function auth(string $username, string $password){
    $sql = "select * from users where username = :username AND
                                      password = :password ";
    
    $smtp = $this->db->prepare($sql);

    $smtp->bindParam(':username', $username);
    $smtp->bindParam(':password', $password);
    $smtp->execute();

    return $smtp->fetchObject("\\App\\Models\\User");

  }


}