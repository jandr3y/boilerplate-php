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
			
			Validator::validate( $body->email, 'Email')->required()->isEmail();
			Validator::validate( $body->password, 'Senha' )->required()->isStrongPassword();
			Validator::validate( $body->name, 'Nome' )->minLength(5)->maxLength(35);
			
		}catch(\Exception $e){
			
			return $response->withJson([ "error" => $e->getMessage() ], 400);
			
		}
		
		$user->setPassword( md5( $body->password ) );
		
		$user->setRealname( $body->name );
		
		$user->setEmail( $body->email );
		
		$user->setRole( 0);
		
		try {
			
			$user->create( $this->db );
			
		}
		catch( \Exception $e ){
			
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
			
		}
		else{
			
			return $response->withJson([ "error" => "É preciso passar o nome de usuário na rota" ]);
			
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
			
		}else{
			
			return $response->withJson([], 404);
			
		}
		
	}
	
	/**
	 * POST /users/{id}
	 * 
	 * Deleta um usuário com base no ID
	 * 
	 * - Se for administrador a regra é ignorada e deleta de qualquer forma.
	 */
	
	public function delete(ServerRequestInterface $request, ResponseInterface $response)
	{
		
		$id = $request->getAttribute('route')->getArgument('id');	
		$current_user = $request->getAttribute('current_user');
		
		$id = intval( $id );
		
		if ( $id == 0 ) {
			
			return $response->withJson([ 'error' => 'Usuário não encontrado' ], 400);
			
		}
		
		if ( $id !== intval( $current_user->id ) ) {
			
			return $response->withJson([ 'error' => 'Você não pode deletar outro usuário' ], 403);
			
		}
		
		$userDAO = User::getDAO( $this->db );
		
		$deleted = $userDAO->delete([
			'id = :id',
			[ 'id' => $id ]
		]);
		
		if( $deleted ) {
			
			return $response->withJson([ 'message' => 'Usuário deletado com sucesso' ], 200);
			
		}
		else{
			
			return $response->withJson([ 'error' => 'Houve um erro desconhecido ao deletar usuário' ], 500);
			
		}
		
	}

	public function update(ServerRequestInterface $request, ResponseInterface $response)
	{
		// Busca o ID vindo da URL
		$id = $request->getAttribute('route')->getArgument('id');
		// Busca o usuário informado pelo Token
		$current_user = $request->getAttribute('current_user');

		$body = json_decode( $request->getBody() );

		if ( $id !== intval( $current_user->id ) ){
			return $response->withJson([ 'error' => 'Você não pode atualizar outro usuário' ]);
		}

		$userDAO = User::getDAO( $this->db );

		$user = $userDAO->findOne([
			'id = :id',
			[
				'id' => $id
			]
		]);

		// TODO: o resto

		if ( $user ) {

		}

	}
	
}
