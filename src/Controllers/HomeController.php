<?php
namespace App\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \App\Models\User;
use \App\Services\Db\UserService;

class HomeController {
  
  private $db;
  public function __construct($container)
  {
      $this->container = $container;
  }

  public function get(Request $request, Response $response)
  {
      $user = new UserService($this->container->db);

      return $response->withJson($user->findOne("id = 1"));
  }

  public function post(Request $request, Response $response)
  {
      // $id = $request->getAttribute('route')->getArgument('id');
      $this->container->logger->error($request);

      
      return $response->withJson(["oi" => "tess"]);
  }


}
