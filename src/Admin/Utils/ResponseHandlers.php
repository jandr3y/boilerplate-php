<?php

namespace App\Admin\Utils;
use Psr\Http\Message\ResponseInterface;

class ResponseHandlers {
  
  /** 
   * Define qual erro mostrar
   * 
   */
  public static function error( ResponseInterface $response, $type = 'DEFAULT', string $custom_message = null, $level = 'info')
  {
    switch( $type ){
      case 'BAD_MODEL':
        return $response->withJson([ 'error' => 'Modelo incorreto' ], 400);
        break;
      case 'NO_IDENTIFIER':
        return $response->withJson([ 'error' => 'Chave de busca não especificada' ], 400);
        break;
      case 'MODEL_NOT_FOUND':
        return $response->withJson([ 'error' => 'Modelo a ser editado não foi encontrado' ], 400);
        break;
      case 'UPDATE_ERROR':
        return $response->withJson([ 'error' => 'Houve um erro ao atualizar modelo' ], 400);
        break;
      case 'CUSTOM':
      
      break;
    }
  }
}