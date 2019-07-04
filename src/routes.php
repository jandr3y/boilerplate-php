<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \App\Controllers\UserController;
use \App\Services\Db\UserService;
use \App\Middlewares\PermissionMiddleware;

// Middlewares
$app->add(new PermissionMiddleware());

// Routes
$app->post('/auth', 'AuthController:auth');

$app->post('/users', 'UserController:post');
$app->get('/users', 'UserController:list');
$app->get('/users/{username}', 'UserController:get');

$app->post('/', function(Request $req, Response $res){
  // print($req->getBody());
  $res->withJson(["teste" => "ok"]);
});