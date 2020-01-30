<?php

namespace App\Models;
use App\Services\Db\Db;

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
	
	private $logger;

	/**
	 * @var string
	 */
	protected $updated_at;

	/**
	 * @var string
	 */
	protected $created_at;

	public function __construct()
	{
		$loggerPath = isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../../logs/db.log';
		$this->logger = new \Monolog\Logger('DbLog');
        $this->logger->pushProcessor(new \Monolog\Processor\UidProcessor());
		$this->logger->pushHandler(new \Monolog\Handler\StreamHandler($loggerPath, \Monolog\Logger::DEBUG));
	}

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

			return new $class_name($db, static::$table, static::$source, static::$hidden);
			
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
	
	public function toArray($config = []){
		
		$object_array = $this->getObjectVars();
		
		unset($object_array['stackMessages']);

		if ( is_array($config['hidden']) ) {
		    return array_filter($object_array, function($element) use ($config) {
		        var_dump($element);
                if ( !is_bool(array_search($element, $config['hidden'])) ) {
                    return false;
                }else{
                    return true;
                }
            }, ARRAY_FILTER_USE_KEY);

        }else{
            if( isset ( static::$hidden ) ){

                if( is_array( static::$hidden )){

                    foreach ( static::$hidden as $field ){

                        unset($object_array[ $field ]);

                    }

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
			
			$this->logger->error('Não foi informado um campo identificador do model ' . static::$source);

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
					$this->logger->debug( $current_model[ $attribute ] );
					$this->logger->debug( $value );
					$this->logger->debug('------------------');
					if ( $current_model[ $attribute ] != $value && $current_model[ $attribute ] != null ){
						
						$diffs[] = $attribute;
						
					}
					
				}
				
				return $diffs;
				
			}
			
		}else{
			
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

		unset($object_array['logger']);
		
		
		// remove timestamps if disabled
		if( isset(static::$timestamps) ){
			unset( $object_array['updatedAt'] );
			unset( $object_array['createdAt'] );
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
		
		$object_vars = $this->getObjectVars();
		
		$columns      = [];
		
		$values       = [];
		
		$bind_values  = [];

        $time_now = (new \DateTime())->format('Y-m-d H:i:s');

		foreach( $object_vars as $key => $value ){

			if ( $key === "id" || $key === "stackMessages" ) continue;


            if ( isset(static::$timestamps) && ($key === 'created_at' || $key === 'updated_at' ) ) {
                $bind_values[":" . $key] = $time_now;
            }else if ( !empty($value) ){
                $bind_values[":" . $key] = $value;
            }else{
                continue;
            }

            $columns[] = $key;
            $values[]  = ":" . $key;
		}


		$columns = implode( ",", $columns );
		$values  = implode( ",", $values );
		
		$query = "INSERT INTO " . static::$table . " ({$columns}) VALUES ({$values})";

		try {
			
			$smtp = $db->prepare( $query );
			
			return $smtp->execute( $bind_values );
			
		}
		catch( \PDOException $e ){
			$this->logger->error('Erro ao criar ' . static::$source);
			$this->logger->error($e->getMessage());

			throw new \Exception(Db::getFriendlyMessage($e));
			
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
		
		$fields_to_update_string = implode(',', $fields_to_update);
		$time_now = (new \DateTime())->format('Y-m-d H:i:s');
		
		// ADD timestamps fields
		if ( is_bool( static::$timestamps ) && static::$timestamps ){
			$fields_to_update_string .= ( count( $fields_to_update ) > 0 ? ',' : '' ) . 'updated_at = :updatedAt';
			$bind_values[':updatedAt'] = $time_now;
		}
		
		$query = "UPDATE " . static::$table . " SET " . $fields_to_update_string  . " WHERE " . static::$primary . " = :id_key";

		try {
			
			$smtp = $db->prepare( $query );
			
			return $smtp->execute( $bind_values );
			
		}
		catch( \PDOException $e ){
			$this->logger->error('Erro ao alterar ' . static::$source);
			$this->logger->error($e->getMessage());
			return false;
			
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
			$this->logger->error('Erro ao deletar ' . static::$source);
			$this->logger->error( $e->getMessage() );
			throw new \Exception( $e->getMessage() );
			
		}

	}
	

	/**
	 * Get the value of createdAt
	 *
	 * @return  string
	 */ 
	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	/**
	 * Set the value of createdAt
	 *
	 * @param  string  $createdAt
	 *
	 * @return  self
	 */ 
	public function setCreatedAt( $createdAt )
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	/**
	 * Get the value of updatedAt
	 *
	 * @return  string
	 */ 
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}

	/**
	 * Set the value of updatedAt
	 *
	 * @param  string  $updatedAt
	 *
	 * @return  self
	 */ 
	public function setUpdatedAt(string $updatedAt)
	{
		$this->updatedAt = $updatedAt;

		return $this;
	}
}
