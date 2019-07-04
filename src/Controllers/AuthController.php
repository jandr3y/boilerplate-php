<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use \Firebase\JWT\JWT;

use \App\Services\Db\UserService;

class AuthController extends BaseController {
  
  private $secret;
  
  public function __construct( \PDO $db, string $secret)
  {
    
    parent::__construct($db);
    
    $this->secret = $jwt;

  }

  public function auth(ServerRequestInterface $req, ResponseInterface $res)
  {
    $userDAO = User::getDAO( $this->db );

    $body = json_decode($req->getBody());

    $user = $userDAO->findOne( [
      "username" => $body->username,
      "password" => md5( $body->password )
    ] );


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