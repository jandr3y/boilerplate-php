<?php

namespace App\Admin\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use \App\Admin\Utils\ResponseHandlers;

/**
 * Essa classe faz rotas genericas para dados para salvar alterações de dados.
 * 
 * Author: Lucas Jandrey
 * Version: 1.0.0
 */
class CrudController {

  /**
   * @var \Slim\Views\PhpRenderer
   */
  private $view;

  /**
   * @var \PDO
   */
  private $db;

  /**
   * @var string
   */
  private $model_name;

  /**
   * @var mixed
   */
  private $blank_model;

  /**
   * @var mixed
   */
  private $_dao;

  /**
   * @var Array[string]
   */
  private $body_attributes = [];
  
  public function __construct( \PDO $db )
  {
    $this->db = $db;
    $this->view = new \Slim\Views\PhpRenderer(__DIR__ . '/../Views/');
  }

  /**
   * Valida o Model Passado e Limpeza de dados
   * Cria DAO e Modelo em branco
   * 
   * @param string nome do modelo
   */
  public function mountObjects( $model_name )
  {
    $this->model_name = $model_name;
    $model_name = '\\App\\Models\\' . ucfirst( $model_name );
    
    if ( class_exists( $model_name ) ) {
      
      $this->blank_model = new $model_name();
      
      $this->_dao = $this->blank_model->getDAO( $this->db );

      return true;
    }else{
      return false;
    }
  }

  /**
   * Armarzena em body_attributes os atributos da classe
   *
   * @param mixed
   */
  public function getBodyAttributes( $body )
  {
    $this->body_attributes = get_object_vars( $body );
    return $this->body_attributes;
  }

  /**
   * POST /admin/crud/{model}
   * 
   * Cria um modelo generico qualquer
   */
  public function create( ServerRequestInterface $request, ResponseInterface $response )
  {

    if ( ! $this->mountObjects( $request->getAttribute('model') ) ){
      return ResponseHandlers::error($response, 'BAD_MODEL');
    }

    $body = (object) $request->getParsedBody();
    
    foreach ( $body as $attribute => $value ){
      $method_name = 'set' . ucfirst($attribute);
      $this->blank_model->$method_name( $body->$attribute );
    }

    if ( $this->blank_model->create( $this->db ) ){
      return $response->withRedirect('/admin/manage/' . $this->model_name . '?message=CREATE_SUCCESS');
    }else{
      return $response->withRedirect('/admin/manage/' . $this->model_name . '?error=CREATE_FAILED&formState=true');
    }    

  }

  /** 
   * DELETE /admin/crud/{model}
   * 
   * Delete um modelo com base no seu campo identificador
   */
  public function delete( ServerRequestInterface $request, ResponseInterface $response )
  {

    if ( ! $this->mountObjects( $request->getAttribute('model') ) ){
      return ResponseHandlers::error($response, 'BAD_MODEL');
    }

    $body = (object) $request->getParsedBody();
    $identifier_key = $this->blank_model::$primary;

    if ( ! empty( $body->$identifier_key ) ){
      $method_name = 'set' . ucfirst( $identifier_key ); 
      $this->blank_model->$method_name( $body->$identifier_key );

      if( $this->blank_model->delete( $this->db ) ){
        return $response->withJson([ 'message' => 'Registro deletado com sucesso' ], 200);
      }else{
        return ResponseHandlers::error($response, 'DELETE_ERROR');
      };
    }else{
      return ResponseHandlers::error($response, 'NO_IDENTIFIER');
    }
  }

  /**
   * UPDATE /admin/crud/{model}
   * 
   * Salva um modelo generico qualquer
   */
  public function update( ServerRequestInterface $request, ResponseInterface $response )
  {
    
    if ( ! $this->mountObjects( $request->getAttribute('model') ) ){
      return ResponseHandlers::error($response, 'BAD_MODEL');
    }

    $body = (object) $request->getParsedBody();
    $attrs = $this->getBodyAttributes( $body );
    $identifier_key = $this->blank_model::$primary;

    if ( ! empty( $body->$identifier_key ) ) {
      
      // Busca o modelo pelo ID
      $current_model = $this->_dao->findOne([
        "id = :id",
        [ 'id' => $body->$identifier_key ]
      ]);

      if ( $current_model ){
        
        // Atualiza as propriedades vindas da requisição (SEM VALIDACAO)
        foreach( $attrs as $attr => $value ){

          $method_name = 'set' . ucfirst($attr);
          $current_model->$method_name( $body->$attr );

        }    
        
        if ( $current_model->update( $this->db ) ){
          return $response->withJson([ 'message' => 'Alterações realizadas com sucesso' ], 200);
        }else{
          return ResponseHandlers::error($response, 'UPDATE_ERROR');
        }

      }else{
        return ResponseHandlers::error($response, 'MODEL_NOT_FOUND');
      }
    }else{
      return ResponseHandlers::error($response, 'NO_IDENTIFIER');
    }


  }

}