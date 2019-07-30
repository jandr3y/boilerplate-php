<?php

namespace App\Admin\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use \Firebase\JWT\JWT;
use \App\Models\User;

class AdminController  {
  
  private $view;
  private $db;
  private $flash;

  public function __construct( \PDO $db, \Slim\Flash\Messages $flash )
  {
    $this->db = $db;
    $this->view = new \Slim\Views\PhpRenderer(__DIR__ . '/../Views/');
    $this->flash = $flash;
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
   * Verifica se há mensagens
   * 
   * @param string Query
   * @return Array|null
   */
  private function hasMessage( $query )
  {
    if ( !empty( $query->message ) ){
      switch($query->message){
        case 'CREATE_SUCCESS':
          return [ 'type' => 'success', 'value' => 'Modelo criado com sucesso'];
        default:
          return [ 'type' => 'info', 'value' => '?? ;)'];
      }
    }

    if ( !empty( $query->error ) ){
      switch($query->error){
        case 'CREATE_FAILED':
          return [ 'type' => 'danger', 'value' => 'Houve um erro ao criar o modelo'];
        default:
          return [ 'type' => 'info', 'value' => '?? ;)'];
      }
    }

    return null;
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

    $query = (object) $req->getQueryParams();

    $modelName = $req->getAttribute('model');
    $modelPath = "\App\Models\\" . ucfirst( $modelName );  
    $model = new $modelPath();
    $dao = $model->getDAO( $this->db );

    $list = $dao->find();

    $data = [
      'models' => $this->getModelFiles(),
      'model' => $modelName,
      'modelArray' => $model->toArray(false),
      'primaryKey' => $model::$primary,
      'formState'  => (!empty( $query->formState ))  ? true : false,
      'table' => $list
    ];

    // Query Messages
    $messages = $this->hasMessage( $query );

    if ( $messages ){
      $data['message'] = $messages;
    }

    $flash_messages = $this->flash->getMessages();
    if ( isset( $flash_messages['pre-object'] ) ){
      $data['preObject'] = $flash_messages['pre-object'][0];
    }
    
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
      " username = :username AND password = :password AND role = 4",
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