<?php
namespace App\Controllers;


use \App\Models\User;
use \App\Services\Db\UserService;

class HomeController {
  
  private $db;

  public function __construct($db)
  {
      $this->db = $db;
  }

  public function __invoke($request, $response) {}

  public function post($request, $response)
  {
      $body = json_decode($request->getBody());
      return $response->withJson($body);
  }

  public function get($request, $response)
  {
      $user = new UserService($this->db);

      return $response->withJson($user->findOne("id = 1"));
  }


}
