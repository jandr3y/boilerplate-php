<?php
namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \App\Models\User;

class HomeController {

  public function get(Request $request, Response $response){
    $user = new User();
    $user->name ="oi";
    $data = array('name' => 'Bob', 'age' => 40);
    
    $response->withHeader('Content-type', 'application/json');
    return $response->withJson($user);
  }
}
