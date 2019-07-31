<?php

namespace App\Admin\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use \App\Admin\Utils\FileManagement;

/**
 * Essa classe implementar as chamadas do Gerador.
 * 
 * Author: Lucas Jandrey
 * Version: 1.0.0
 */
class GeneratorController {

  /**
   * @var \Slim\Views\PhpRenderer
   */
  private $view;

  /**
   * @var \PDO
   */
  private $db;

  /**
   * @var \Slim\Flash\Messages
   */
  private $flash;

  public function __construct( \PDO $db, \Slim\Flash\Messages $flash )
  {

    $this->db = $db;
    $this->flash = $flash;
    $this->view =  new \Slim\Views\PhpRenderer(__DIR__ . '/../Views/');
  
  }

  public function index( ServerRequestInterface $request, ResponseInterface $response )
  {

    $data = [
      'models' => FileManagement::getModelFiles()
    ];

    $this->view->render($response, 'generator.phtml', $data);
  }

}