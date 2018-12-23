<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \App\Controllers\UserController;
use \App\Services\Db\UserService;

// Routes
$app->post('/users', 'UserController:post');
$app->get('/users', 'UserController:list');
$app->get('/users/{id}', 'UserController:get');

$app->post('/', function(Request $req, Response $res){
  // print($req->getBody());
  $res->withJson(["teste" => "ok"]);
});