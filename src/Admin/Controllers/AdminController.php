<?php

namespace App\Admin\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use \Firebase\JWT\JWT;
use \App\Models\User;

class AdminController  {
  
  private $secret;
  private $view;
  private $db;
  
  public function __construct( \PDO $db, string $secret)
  {
    $this->db = $db;
    $this->view = new \Slim\Views\PhpRenderer(__DIR__ . '/../Views/');
    $this->secret = $secret;
  }

  /**
   * Busca os modelos disponiveis.
   */
  private function getModelFiles()
  {
    $files = scandir(__DIR__ . '/../../Models');
    
    if ( is_array( $files ) ){
      foreach( $files as $key => $file ) {
        if ( strpos($file, 'php') > 0 ){
          $files[ $key ] = explode(".", $file)[0];
        } else {
          unset( $files[ $key ] );
        }
      }
    }

    return array_filter( $files, function( $file ) {
      if ( $file != 'Model' ) {
        return true;
      }else{
        return false;
      }
    });
  }

  /**
   * Faz a trataiva do erro
   * 
   * @param string $type tipo do erro
   * @return Array Valores da calsse CSS e do valor
   */
  private function errorHandler( $type )
  {
    if ( $type === "auth" ) {
      return [ "type" => "danger", "value" => "Senha ou usuário incorretos" ];
    }

    return false;
  }

  /**
   * GET /admin/login
   * 
   * Renderiza página de Login
   */
  public function login(ServerRequestInterface $req, ResponseInterface $res)
  {
    $params = (object) $req->getQueryParams();
    $data = [];

    if ( isset( $params->error ) ){
      $data['message'] = $this->errorHandler( $params->error );
    }

    $this->view->render($res, 'login.phtml', $data);
  }

  /** 
   *  GET /admin
   *  
   *  Retorna página Dashboard Inicial
   */
  public function home(ServerRequestInterface $req, ResponseInterface $res)
  {
    if ( empty( $_SESSION['user_id'] ) ) {
      return $res->withRedirect('/admin/login');
    }

    $data = [
      'models' => $this->getModelFiles()
    ];
    

    $this->view->render($res, 'index.phtml', $data);
  }

  /**
   * Desloga do Painel Adminitrador
   */
  public function logout(ServerRequestInterface $req, ResponseInterface $res)
  {
    session_destroy();
    return $res->withRedirect('/admin/login');
  }

  /**
   * GET /admin/manage/{model}
   * 
   * 
   */
  public function list(ServerRequestInterface $req, ResponseInterface $res)
  {
    if ( empty( $_SESSION['user_id'] ) ) {
      return $res->withRedirect('/admin/login');
    }

    $modelName = $req->getAttribute('model');
    $modelPath = "\App\Models\\" . ucfirst( $modelName );  
    $model = new $modelPath();
    $dao = $model->getDAO( $this->db );

    $list = $dao->find();

    $data = [
      'models' => $this->getModelFiles(),
      'model' => $modelName,
      'modelArray' => $model->toArray(false),
      'table' => $list
    ];

    $this->view->render($res, 'manage.phtml', $data);
  }

  /**
   * POST /admin/auth
   * 
   * Autentica o Usuário
   */
  public function auth(ServerRequestInterface $req, ResponseInterface $res)
  {
    
    $userDAO = User::getDAO( $this->db );

    $body = (object) $req->getParsedBody();

    $user = $userDAO->findOne( [
      " username = :username AND password = :password AND role = 8",
      [ 
        "username" => $body->username,
        "password" => md5( $body->password )
      ]
    ] );


    if( $user ){
      $_SESSION['user_id'] = $user->getId();
      return $res->withRedirect('/admin');
    }else{
      return $res->withRedirect('/admin/login?error=auth');
    }
  }

}