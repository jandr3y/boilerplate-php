<?php

namespace Tests\Functional;

class UserTest extends BaseTestCase
{

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

    public function testCriarUsuarioSemInformacoes()
    {
        $data = [ 'username' => '@##' ];
        $response = $this->runApp('POST', '/users', $data);
        $this->assertEquals(400, $response->getStatusCode());

        $data['username'] = 'luacksss';
        $data['password'] = 'test123';
        $response = $this->runApp('POST', '/users', $data);
        $this->assertEquals(400, $response->getStatusCode());

        $data['password'] = 'test1234@';
        $data['name'] = 'Lucs';
        $response = $this->runApp('POST', '/users', $data);
        $this->assertEquals(400, $response->getStatusCode());

        $data['name'] = 'Lucass Jandreeeeeeeeeeeeeey andnrnannananannrna nrnarn rnanrnarnarnnarnrnanrna nrnarn';
        $response = $this->runApp('POST', '/users', $data);
        $this->assertEquals(400, $response->getStatusCode());

        $data['name'] = 'Lucas Jandrey';
        $response = $this->runApp('POST', '/users', $data);
        $this->assertEquals(200, $response->getStatusCode());
    }

}
