<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \App\Controllers\HomeController;
use \App\Services\Db\UserService;
// Routes
$app->get('/', HomeController::class . ':get');
$app->post('/{id}', HomeController::class . ':post');
