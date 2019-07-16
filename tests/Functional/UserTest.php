<?php

namespace Tests\Functional;

class UserTest extends BaseTestCase
{
    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testListarUsuarioSemToken()
    {
        $response = $this->runApp('GET', '/users');
        
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testListarUsuarioComToken()
    {
      $response = $this->runApp('GET', '/users', 
      null, 
      'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImphbmRyZXkiLCJuYW1lIjoiTHVjYXMgSmFuZHJleSIsInJvbGUiOiIxIn0.epGPk8kp16tIR2TJ7SMa-uRkCJrfCdkSTsyJXgf5fng'
      );

      $this->assertEquals(200, $response->getStatusCode());
    }

}