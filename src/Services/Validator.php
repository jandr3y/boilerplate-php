<?php

namespace App\Services;

class Validator {
  public static function isStrongPassword(string $password)
  {
    if (strlen($password) >= 6){
      if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $password)){
        return md5($password);
      }else{
        throw new \Exception("Senha deve conter caracteres especiais [@, $, &...]");
      }
    }else{
      throw new \Exception("Senha deve conter mais de 5 caracteres");
    }
  }
}