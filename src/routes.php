<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \App\Controllers\HomeController;
// Routes
$app->get('/', [HomeController::class, "get"]);


