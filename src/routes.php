<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \App\Controllers\HomeController;
use \App\Services\Db\UserService;

// Routes
$app->get('/', 'HomeController:get');
$app->post('/d', 'HomeController:post');

$app->post('/', function(Request $req, Response $res){
  // print($req->getBody());
  $res->withJson(["teste" => "ok"]);
});