<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \App\Controllers\HomeController;
use \App\Services\Db\UserService;
// Routes
$app->get('/', [HomeController::class, "get"]);


$app->get('/test', function(Request $req, Response $res){
  $service = new UserService($this->db);

  
  $res->withHeader('Content-type', 'application/json');
  return $res->withJson($service->findOne("id = 1"));
});