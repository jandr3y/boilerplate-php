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
			
		}
		else{
			
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
		
		$object_array = $this->getObjectVars();
		
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
   * Retorna o valor do identificador
   */
	private function getIdentifier()
	{
		
		if ( ! empty( static::$primary ) ){
			
			$method_name  = 'get' . ucfirst( static::$primary );
			
			$id_key       = $this->$method_name();
			
			return $id_key;
			
		}
		else{
			
			throw new \Exception('Não foi informado um campo identificador.');
			
		}
		
	}
	
	/**
   * Verifica as diferenças do modelo atual para oque esta registrado no banco
   * 
   * @return Array[string] Campos com diferença
   */
	public function getDiffs( \PDO $db )
	{
		
		if ( $this->getIdentifier() ){
			
			$dao = $this->getDAO( $db );
			
			$db_object = $dao->findOne([ 
				'id = :id',
				[ 'id' => $this->getIdentifier() ]
			]);
			
			if ( $db_object ){
				
				$diffs = [];
				
				$db_object = $db_object->toArray();
				
				$current_model = $this->toArray();
				
				foreach( $db_object as $attribute => $value ){
					
					if ( $current_model[ $attribute ] != $value && $current_model[ $attribute ] != null ){
						
						$diffs[] = $attribute;
						
					}
					
				}
				
				return $diffs;
				
			}
			
		}
		else{
			
			return false;
			
		}
		
	}
	
	/**
   * Retorna as variaveis do objeto atual
   * 
   * @return Array
   */
	public function getObjectVars()
	{
		
		$object_array = get_object_vars($this);
		
		unset($object_array['stackMessages']);
		
		unset($object_array['diffs']);
		
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
		
		$object_vars = $this->getObjectVars();
		
		$columns      = [];
		
		$values       = [];
		
		$bind_values  = [];
		
		foreach( $object_vars as $key => $value ){
			
			if ( $key === "id" || $key === "stackMessages" ) continue;
			
			// 			create fields to insert
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
			
		}
		catch( \PDOException $e ){
			
			throw new \Exception( $e->getMessage() );
			
		}
		
	}
	
	/**
   * Cria o objeto no banco baseado em seus parametros.
   * @param \PDO Conexão do Banco
   * @return bool True se alterou
   */
	public function update( \PDO $db )
	{
		
		$object_vars = $this->getObjectVars();
		
		$diffs = $this->getDiffs( $db );
		
		$fields_to_update = [];
		
		$bind_values      = [];
		
		foreach( $diffs as $diff ){
			
			$method_name = 'get' . ucfirst( $diff );
			
			$fields_to_update[] = $diff . ' = :' . $diff;
			
			$bind_values[':' . $diff] = $this->$method_name();
			
		}
		
		$bind_values[':id_key'] = $this->getIdentifier();
		
		$query = "UPDATE " . static::$table . " SET " . implode(',', $fields_to_update) . " WHERE " . static::$primary . " = :id_key";
		
		try {
			
			$smtp = $db->prepare( $query );
			
			return $smtp->execute( $bind_values );
			
		}
		catch( \PDOException $e ){
			
			throw new \Exception( $e->getMessage() );
			
		}
		
	}

	/**
	 * Deleta o registro do banco
	 * 
	 */
	public function delete( $db )
	{

		$bind_values = [];

		$query = "DELETE FROM " . static::$table . " WHERE " . static::$primary . " = :id_key ";
		$bind_values[':id_key'] = $this->getIdentifier(); 

		try {
			
			$smtp = $db->prepare( $query );
			
			return $smtp->execute( $bind_values );
			
		}
		catch( \PDOException $e ){
			
			throw new \Exception( $e->getMessage() );
			
		}

	}
	
}
