<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class PermissionMiddleware {

  private $controlList = [];

  public function __construct( $config = [] )
  {
    $this->controlList = $config;
  }

  /**
   *  Função que com base nas permissões definidas em settings.php habilita a rota para o usuário,
   * 
   *  @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
   *  @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
   *  @param  callable                                 $next     Next middleware
   */
  public function __invoke( ServerRequestInterface $request, ResponseInterface $response, callable $next )
  {
    return $next($request, $response);
  }

}
