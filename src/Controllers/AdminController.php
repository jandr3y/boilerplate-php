<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use \Firebase\JWT\JWT;
use \App\Models\User;

class AdminController extends BaseController {
  
  private $secret;
  private $view;
  
  public function __construct( \PDO $db, string $secret, $renderer = null)
  {
    
    parent::__construct($db);
    $this->view = $renderer;
    $this->secret = $secret;

  }

  
  public function index(ServerRequestInterface $req, ResponseInterface $res)
  {
    
    $this->view->render($res, 'admin/index.phtml');
  }

}