<?php

namespace App\Admin;

class AdminRoutes {
  public function __invoke(\Slim\App $app)
  {
    $container = $app->getContainer();
    $container['AdminController'] = function ( $c ) {
      $db = $c->get('db');
      return new \App\Admin\Controllers\AdminController($db, $c['settings']['jwtSecret'], $c->get('renderer'));
    };
    

    $app->post('/admin/auth', 'AdminController:auth'); 
    $app->get('/admin/login', 'AdminController:login'); 
    $app->get('/admin', 'AdminController:home'); 
  }
}