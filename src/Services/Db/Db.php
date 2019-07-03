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
	
	public function __construct($db, $table, $model){
		
		$this->db = $db;
		
		$this->table = $table;
		
		$this->model = $model;
		
	}
  
  /**
   * Busca apenas um registro
   * 
   * @param string $where | Condição adicional
   * @return stdClass Objeto buscado
   */
	public function findOne($where = ""){

		// TODO: WHERE and Params with BindValue
    
    $smtp = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$where}");
		
		$smtp->execute();
		
		$model = "\\App\\Models\\";
		
		$model .= $this->model;
		
		return $smtp->fetchObject($model);
		
	}
  
  /**
   * Busca varios registros
   * 
   * @param string $where | Condição adicional
   * @param string $limit | Limitador de busca
   * @return stdClass Objeto buscado
   */
	public function find($where = null, $limit = null){
    
    // TODO: WHERE and Params with BindValue
    
		$sql = "select * from {$this->table} ";
		
		if(isset($where))
		$sql .= "where {$where} ";
		
		if(isset($limit))
		$sql .= "limit {$limit}";
		
		$smtp = $this->db->prepare($sql);
		
		$smtp->execute();
		
		return $smtp->fetchAll();
		
	}
	
	public function save()
	{
		
	}
	
}
