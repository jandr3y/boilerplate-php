<?php
namespace App\Controllers;


use \App\Models\User;
use \App\Services\Db\UserService;
use \App\Services\Validator;

class UserController {
  
  private $db;

  public function __construct($db)
  {
      $this->db = $db;
  }

  public function __invoke($request, $response) {}

  public function post($request, $response)
  {
      $body = json_decode($request->getBody());
      $user = new User();
      
      try {
        $user->username = $body->username;
        $user->password = Validator::isStrongPassword($body->password);
        $user->name     = $body->name;
      } catch(\Exception $e){
        return $response->withJson([ "error" => $e->getMessage() ]);
      }
       
      $userService = new UserService($this->db);
      
      $result = $userService->create($user);

      return $response->withJson([ "result" => $result ]);
  }

  public function get($request, $response)
  {
      $id = $request->getAttribute('route')->getArgument('id');
      $user = new UserService($this->db);
      return $response->withJson($user->findOne("id = " . $id));
  }

  public function list($request, $response){
      $users = (new UserService($this->db))->find();
      return $response->withJson($users);
  }


}
