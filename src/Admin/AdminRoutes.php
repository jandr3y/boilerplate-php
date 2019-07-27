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
    
    $container['CrudController'] = function ( $c ) {
      $db = $c->get('db');
      return new \App\Admin\Controllers\CrudController($db, $c->get('renderer'));
    };

    $app->post('/admin/auth', 'AdminController:auth'); 
    $app->get('/admin/login', 'AdminController:login');
    $app->get('/admin/manage/{model}', 'AdminController:list'); 
    $app->get('/admin/logout', 'AdminController:logout');
    $app->get('/admin', 'AdminController:home'); 

    // Generic Crud
    $app->post('/admin/crud/{model}', 'CrudController:create');
    // $app->get('/admin/crud/{model}', 'CrudController:read'); -- rota não necessária, no momento. :/
    $app->put('/admin/crud/{model}', 'CrudController:update');
    $app->delete('/admin/crud/{model}', 'CrudController:delete');

  }
}