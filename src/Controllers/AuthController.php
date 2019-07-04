<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use \Firebase\JWT\JWT;
use \App\Models\User;

class AuthController extends BaseController {
  
  private $secret;
  
  public function __construct( \PDO $db, string $secret)
  {
    
    parent::__construct($db);
    
    $this->secret = $secret;

  }

  /**
   * POST /auth
   * 
   * Retorna um token se for o login correto ou padrão erro
   */
  public function auth(ServerRequestInterface $req, ResponseInterface $res)
  {
    $userDAO = User::getDAO( $this->db );

    $body = json_decode($req->getBody());

    $user = $userDAO->findOne( [
      " username = :username AND password = :password ",
      [ 
        "username" => $body->username,
        "password" => md5( $body->password )
      ]
    ] );


    if($user->getId()){
      
      $token = JWT::encode([
        "username"  => $user->getUsername(),
        "name"      => $user->getName()
      ], $this->secret);

      return $res->withJson(["token" => $token ]);
    
    }else{
      return $res->withJson(["error" => "Usuário ou senha incorretos" ]);
    }
  }

}