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
   * GET /users/{id} 
   * 
   * Busca somente um usuário com base no id
   * @return User
   */
	public function post(ServerRequestInterface $request, ResponseInterface $response)
	{
		
		$body = json_decode($request->getBody());
		
		$user = new User();
		
		try {
		
			$user->setUsername( Validator::isUsername( $body->username ) );
			
			$user->setPassword(  Validator::isStrongPassword( $body->password ) );
			
			$user->setName( $body->name );
			
		}
		
		catch(\Exception $e){
			
			return $response->withJson([ "error" => $e->getMessage() ]);
			
		}
		
		$userService = new UserService($this->db);
		
		$result = $userService->create($user);
		
		return $response->withJson([ "result" => $result ]);
		
	}
	
	/**
   * GET /users/{id} 
   * 
   * Busca somente um usuário com base no id
   * @return User
   */
	
	public function get(ServerRequestInterface $request, ResponseInterface $response)
	{
		
		$id = $request->getAttribute('route')->getArgument('id');
		
		if ( $id ) {
			
			$user = User::getDAO($this->db)->findOne(" id = " . $id);
			
			return $response->withJson( $user->toArray() );
			
		}
		else{
			
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
		
		$users = $userDAO->find();
		
		if ( is_array( $users ) ) {
			
			return $response->withJson($users);
			
		}
		
		else{
			
			return $response->withJson([], 404);
			
		}
		
	}
	
}
