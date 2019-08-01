<?php
/**
 * Classe que possui os metodos necessários para conversação com o facebrooklyn
 * Fonte: https://developers.facebook.com/docs/php/howto/example_facebook_login/?locale=pt_BR
 */
namespace App\Services\Authorization;

class FacebookAuth {
  
  /**
   * @var \Facebook\Facebook
   */
  private $fb;

  public function __construct()
  {
    $this->fb = new \Facebook\Facebook([
      'app_id' => '{app-id}',
      'app_secret' => '{app-secret}',
      'default_graph_version' => 'v2.10',
    ]);

    // TODO: Log System
  }

  public function callback()
  {

    $helper = $this->fb->getRedirectLoginHelper();

    try {
      $accessToken = $helper->getAccessToken();
    } catch( \Facebook\Exceptions\FacebookResponseException $e ) {
      // TODO: Tratar exceções da API do facebook
    } catch( \Facebook\Exceptions\FacebookResponseException $e ) {

    }
  }

}