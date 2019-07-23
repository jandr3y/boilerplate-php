<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface;

use Psr\Http\Message\ResponseInterface;

use \Firebase\JWT\JWT;

class PermissionMiddleware {
	
	private $controlList = [];
	
	private $secret = "";
	
	public function __construct( $config = [], string $secret )
	{
		
		$this->secret = $secret;
		
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
		
		if ( strpos( $_SERVER['REQUEST_URI'], 'admin' ) ) {
			return $next($request, $response);
		}

		$headers = $request->getHeaders();
		
		// array de rotas
		$public = $this->controlList['public'];
		
		$user   = $this->controlList['user'];
		
		$admin  = $this->controlList['admin'];

		$current_path = $request->getAttribute('route')->getPattern();

		$method = $request->getMethod();
		
		// 		Checa se a rota é publica
		if ( is_array($public) ) {
			
			$paths = (isset($public[ strtolower($method) ])) ? $public[ strtolower($method) ] : null;
			
			if( is_array( $paths ) ){
				
				foreach($paths as $path){
					
					if ( $path == $current_path ) {
						
						return $next($request, $response);
						
					}
					
				}
				
			}
			
		}
		
		if ( isset( $headers['HTTP_AUTHORIZATION'] ) ) {
			
			try {
				
				$user_token = JWT::decode( $headers['HTTP_AUTHORIZATION'][0],  $this->secret, array('HS256') );
				
			}
			catch( \Firebase\JWT\SignatureInvalidException $e ){
				
				return $response->withJson([ 'error' => 'Você não tem permissão para acessar esta rota' ], 403);
				
			}
			
			$request = $request->withAttribute('current_user', $user_token);
			
			if( isset($user_token->role) ){
				
				switch( $user_token->role ){
					// Usuário normal
					case 1: 
					$paths = $user[ strtolower($method) ];
					
					foreach( $paths as $path ){
						
						if ( $path == $current_path ){
							
							return $next($request, $response);
							
						}
						
					}
					
					break;
					// Administrador ...
					case 2:
					$paths = $admin[ strtolower($method) ];
					
					foreach( $paths as $path ){
						
						if ( $path == $current_path ){
							
							return $next($request, $response);
							
						}
						
					}
					
					break;
					
					default:
					return $response->withJson([ 'error' => 'Você não tem permissão para acessar esta rota' ], 403);
					
					break;
					
				}
				
			}
			
		}
		else {
			
			return $response->withJson([ 'error' => 'Você não tem permissão para acessar esta rota' ], 403);
			
		}
		
		return $next($request, $response);
		
	}
	
}
