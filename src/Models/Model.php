<?php

namespace App\Models;

/**
 * Classe Model
 *
 * Classe que deve ser extendida ao criar um novo Model
 *
 * @package     App
 * @subpackage  Models
 * @author      Lucas Jandrey <lucas@jandrey.dev>
 */
class Model {

  /**
   * Retorna classe de serviço
   * 
   * @param PDO $db Conexão com o banco de dados
   * @return stdClass|bool Objeto Service do Modelo ou se não conseguir a referencia false.
   */
  public static function getDAO( PDO $db )
  {
    if ( isset( static::$source ) ) {

      $class_name = "\App\Services\Db\\" . static::$source . "Service"; 
      return new $class_name($db, static::$table, static::$source);

    }else{
      return false;
    }
  }

  /**
   * Retornar o objeto atual em array
   * 
   * @return Array Objeto estanciado em Array
   */
  public function toArray(){
    return get_object_vars($this);
  }

}

