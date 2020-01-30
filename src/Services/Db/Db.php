<?php
namespace App\Services\Db;

class Db {
  
  /**
   * @var PDO
   */
	public $db;
  
  /**
   * @var string
   */
	public $table;
  
  /**
   * @var stdClass | Qualquer objeto extendido por Model
   */
  public $model;
  
  /**
   * @var \Monolog\Logger
   */
  private $logger;

  public $hidden;

	
	public function __construct($db, $table, $model, $hidden = null){
		
		$this->db = $db;
		
		$this->table = $table;
		$this->hidden = $hidden;
        $this->model = $model;
    
    $loggerPath = isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../../../logs/db.log';
		$this->logger = new \Monolog\Logger('DbLog');
    $this->logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $this->logger->pushHandler(new \Monolog\Handler\StreamHandler($loggerPath, \Monolog\Logger::DEBUG));
	}
  
  /**
   * Busca apenas um registro
   * 
   * @param Array $where | Condição adicional
   * @return stdClass Objeto buscado
   */
	public function findOne(Array $where = []){

    $where = $this->getWhereCondition( $where );

    try {

      $smtp = $this->db->prepare("SELECT * FROM {$this->table} {$where->query}");
      
      $smtp->execute( $where->params );
      
      $model = "\\App\\Models\\";
      
      $model .= $this->model;
      
      return $smtp->fetchObject($model);
    
    }catch( \PDOException $e ){
      $this->logger->error('Não foi possivel buscar um ' . $this->model);
      $this->logger->error($e->getMessage());
      return false;

    }
		
  }
  
  /**
   * Busca varios registros
   * 
   * @param Array $where | Condição adicional
   * @param int $limit | Limitador de busca
   * @return stdClass Objeto buscado
   */
	public function find(Array $where = [], $config = []){
    
        $where = $this->getWhereCondition( $where );
    
		$sql = "select * from {$this->table} {$where->query}";
		
		
		if(isset($limit)){
            $sql .= "limit {$limit}";
        }
    
    try {

      $smtp = $this->db->prepare($sql);
      
      $smtp->execute( $where->params );
      
      $result = $smtp->fetchAll();
      $finalArray = [];


      foreach( $result as $row ) {
          $finalArray[] = array_filter($row, function($element) use ($config)  {
              if ( is_array($config['hidden']) && !is_bool(array_search($element, $config['hidden'])) ) {
                  return false;
              }else{
                  return true;
              }
          }, ARRAY_FILTER_USE_KEY);
      }

      return $finalArray;
    
    }catch(\PDOException $e){

      $this->logger->error('Não foi possivel buscar ' . $this->model);
      $this->logger->error($e->getMessage());
      return false;
    
    }
		
	}
  
  private function getWhereCondition( Array $where )
  {
    if( count( $where ) < 1 ){
      return (object) [ "query" => "", "params" => [] ];
    }

    $params = [];

    $where_query = " WHERE 1=1 AND " . $where[0];

    if ( count( $where[1] ) > 0 ) {
      foreach( $where[1] as $column => $value ){
        $params[":" . $column] = $value;
      }
    }
    
    return (object) [ "query" => $where_query, "params" => $params ];
  }

  public function delete( Array $where )
  {
    $where = $this->getWhereCondition( $where );

    $query = "DELETE FROM {$this->table} " . $where->query . PHP_EOL;

    try {
      
      $smtp = $this->db->prepare( $query );
      return $smtp->execute( $where->params );

    }catch( \PDOException $e ){
      // TODO: Log
      var_dump($e->getMessage()); die();
      return false;
    }

  }

  public static function getFriendlyMessage(\Exception $e)
  {

      $arr_uk = explode('uk-', $e->getMessage());

      if ( count($arr_uk) > 1 ) {
          return "O campo " . substr($arr_uk[1], 0, -1) . " ja esta sendo utilizado";
      }
  }
}
