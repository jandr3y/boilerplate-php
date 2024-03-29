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

    $body = (object) json_decode($req->getBody());

    $user = $userDAO->findOne( [
      " email = :email AND password = :password ",
      [ 
        "email" => $body->email,
        "password" => md5( $body->password )
      ]
    ] );


    if( $user ){
      
      $token = JWT::encode([
        "id"        => $user->getId(),
        "email"  => $user->getEmail(),
        "name"      => $user->getName(),
        "role"      => $user->getRole()
      ], $this->secret);

      return $res->withJson(["token" => $token ]);
    
    }else{
      return $res->withJson(["error" => "Usuário ou senha incorretos" ], 403);
    }
  }

}