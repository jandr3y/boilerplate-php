<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \App\Controllers\UserController;
use \App\Services\Db\UserService;
use \App\Middlewares\PermissionMiddleware;
$container = $app->getContainer();
// Middlewares
$app->add(new PermissionMiddleware($container['settings']['acl'], $container['settings']['jwtSecret']));

// Routes
$app->post('/auth', 'AuthController:auth');

$app->post('/users', 'UserController:post');
$app->get('/users', 'UserController:list');
$app->get('/users/{username}', 'UserController:get');

$app->get('/', function(Request $req, Response $res){
  return $res->withJson([
    "name"    => "API BP",
    "version" => "1.0.1"
  ]);
});