<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \App\Services\Db\UserService;
use \App\Middlewares\PermissionMiddleware;
use \App\Admin\AdminRoutes;

$container = $app->getContainer();

// Admin Panel Module
//$admin = new AdminRoutes;
//$admin( $app );

// Middlewares
$app->add(new PermissionMiddleware($container['settings']['acl'], $container['settings']['jwtSecret']));

// Routes
$app->post('/auth', 'AuthController:auth');

$app->post('/users', 'UserController:post');
$app->get('/users', 'UserController:find');
$app->get('/users/{id}', 'UserController:get');
$app->delete('/users/{id}', 'UserController:delete');
$app->put('/users/{id}', 'UserController:update');

$app->get('/', function(Request $req, Response $res){
  return $res->withJson([
    "name"    => "API BP",
    "version" => "1.0.1"
  ]);
});