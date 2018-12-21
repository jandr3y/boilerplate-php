<?php
namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \App\Models\User;
use \App\Services\Db\UserService;

class HomeController {
  
  public $db;

  public function __construct($db){
    $this->db = $db;
  }

  public function setDb($db){
    $this->db = $db;
  }

  public function get(Request $request, Response $response){
    $user = new User();
    $response->withHeader('Content-type', 'application/json');
    return $response->withJson($user);
  }
}
