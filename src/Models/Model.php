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

  private $stackMessages = [];

  /**
   * Retorna classe de serviço
   * 
   * @param PDO $db Conexão com o banco de dados
   * @return stdClass|bool Objeto Service do Modelo ou se não conseguir a referencia false.
   */
  public static function getDAO( \PDO $db )
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
   * @param int $hidden se deve ocultar campos privados
   * @return Array Objeto estanciado em Array
   */
  public function toArray($hidden = true){
    
    $object_array = get_object_vars($this);
    unset($object_array['stackMessages']);
    
    if( isset ( static::$hidden ) ){
      if( is_array( static::$hidden ) && $hidden){
        foreach ( static::$hidden as $field ){
          unset($object_array[ $field ]);
        }
      }
    }

    return $object_array;
  }

  /**
   * Salva o objeto atual no banco.
   * 
   * @param \PDO conexão com o banco
   * @return bool True se salvou
   * @throws \Exception caso tenha algum erro
   */
  public function create( \PDO $db )
  {

    $object_vars = get_object_vars( $this );
    
    $columns      = [];
    $values       = [];
    $bind_values  = [];

    foreach( $object_vars as $key => $value ){
      
      if ( $key === "id" || $key === "stackMessages" ) continue;

      // create fields to insert
      $columns[] = $key;

      $values[]  = ":" . $key;

      $bind_values[":" . $key] = $value;

    }
    
    $columns = implode( ",", $columns );
    $values  = implode( ",", $values );
    
    $query = "INSERT INTO " . static::$table . " ({$columns}) VALUES ({$values})";
    
    try {
      
      $smtp = $db->prepare( $query );

      return $smtp->execute( $bind_values );

    }catch( \PDOException $e ){
      throw new \Exception( $e->getMessage() );
    }

    
  }


}

