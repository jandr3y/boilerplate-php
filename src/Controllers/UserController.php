<?php
namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use \App\Models\User;

use \App\Services\Validator;

/**
 * Rotas do usuário
 *
 * Rotas da API que fazem alterações em usuários
 *
 * @package     App
 * @subpackage  Controllers
 * @author      Lucas Jandrey <lucas@jandrey.dev>
 */
class UserController extends BaseController {
	
	/**
   * POST /users 
   * 
   * Cria um usuário
   * @return User
   */
	public function post(ServerRequestInterface $request, ResponseInterface $response)
	{
		
		$body = json_decode( $request->getBody() );
		
		$user = new User();
		
		try {
		
			Validator::isUsername( $body->username );
			Validator::isStrongPassword( $body->password );
			Validator::minLength( $body->name, 5, 'Nome' );
			Validator::maxLength( $body->name, 35, 'Nome' );
			
		}catch(\Exception $e){
			
			return $response->withJson([ "error" => $e->getMessage() ]);
			
		}

		$user->setPassword( md5( $body->password ) );
		$user->setName( $body->name );
		$user->setUsername( $body->username );
		
		try { 
		
			$user->create( $this->db );
		
		}catch( \Exception $e ){

			return $response->withJson([ "error" => $e->getMessage() ]);

		}
		
		
		return $response->withJson([ "message" => "Usuário criado com sucesso" ]);
		
	}
	
	/**
   * GET /users/{id} 
   * 
   * Busca somente um usuário com base no id
   * @return User
   */
	
	public function get(ServerRequestInterface $request, ResponseInterface $response)
	{
		
		$username = $request->getAttribute('route')->getArgument('username');
		
		if ( $username ) {
			
			$user = User::getDAO($this->db)->findOne([ 
			  	'username = :username', 
				[ 'username' => $username ] 
			]);
			
			return $response->withJson( $user->toArray() );
			
		}else{
			
			return $response->withJson([ "error" => "É preciso passar o ID na rota" ]);
			
		}
		
	}
	
	/**
   * GET /users
   * 
   * Lista os usuários 
   * 
   * @return Array<User>
   */
	
	public function list(ServerRequestInterface $request, ResponseInterface $response)
	{
		
		$userDAO = User::getDAO($this->db);
		
		$users = $userDAO->find([
			'id > :id',
			[ 'id' => 1 ]
		]);
		
		if ( is_array( $users ) ) {
			
			return $response->withJson($users);
			
		}
		
		else{
			
			return $response->withJson([], 404);
			
		}
		
	}
	
}
