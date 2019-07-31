<?php

namespace App\Admin\Utils;

class GlobalView {
	
	/**
   * Limpa html
   */
  public function __($string)
  {
    return htmlspecialchars($string);
  }
}