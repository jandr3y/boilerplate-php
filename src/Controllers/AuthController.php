<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

use \App\Services\Db\UserService;

class AuthController extends BaseController {
  
  private $db;

  private $json;
  
  public function __construct($db, $jwt){
    $this->db = $db;
    $this->json = $jwt;
  }

  public function __invoke($request, $response) { }

  public function auth(Request $req, Response $res){
    $userService = new UserService($this->db);

    $body = json_decode($req->getBody());

    $user = $userService->auth($body->username, md5($body->password));

    if($user->id){
      $token = JWT::encode([
        "username" => $user->username,
        "name" => $user->name
      ], $this->json);

      return $res->withJson(["token" => $token ]);
    }else{
      return $res->withJson(["error" => "Usuário ou senha incorretos." ]);
    }
  }

}