<?php

namespace App\Services;

class Validator {

  /**
   * Valida se é uma senha forte
   *  - Deve conter caracteres especiais
   *  - Deve ter mais de 5 digitos
   * 
   * @param string $password | Valor
   * @return string Senha encriptada
   * @throws Exception Pode lançar uma exceção com a mensagem de erro
   */
  public static function isStrongPassword( $password )
  {
    if (strlen($password) >= 6){
      if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $password)){
        return md5($password);
      }else{
        throw new \Exception("Senha deve conter caracteres especiais [@, $, &...]");
      }
    }else{
      throw new \Exception("Senha deve conter mais de 5 caracteres");
    }
  }

  /**
   * Valida se o nome de usuário passado é um usuário valido
   * Restrições:
   *  - Deve conter apenas letras e numeros
   *  - Todas as letras devem ser minusculas
   *  - Deve conter de 4 a 20 caracteres 
   * 
   * @param string $username | Nome de usuário a ser válidado
   * @return string Retorna usuário se for valido
   * @throws Exception Pode lançar uma exceção com a mensagem de erro
   */
  public static function isUsername( $username )
  {
    if ( preg_match('/^[a-z\d_]{4,20}$/i', $username) ) {
      return $username;
    }else{
      throw new \Exception("Nome de usuário deve conter apenas números e letras");
    }
  }

  /**
   * Valida se tem uma quantidade minima de caracteres
   * 
   * @param string $valor a ser validado
   * @param int $size tamanho minimo
   * @param string $field_name Campo como alias de mensagem de erro
   * @return string $valor normal
   * @throws \Exception Mensagem de erro se for menor
   */
  public static function minLength( $value, int $size, string $field_name = "campo" )
  {
    if ( strlen( $value ) <= $size ) {
      throw new \Exception("O campo {$field_name} deve conter no mínimo {$size} caracteres");
    }else{
      return $value; 
    }
  }

  /**
   * Valida se tem uma quantidade maxima de caracteres
   * 
   * @param string $valor a ser validado
   * @param int $size tamanho maximo
   * @param string $field_name Campo como alias de mensagem de erro
   * @return string $valor normal
   * @throws \Exception Mensagem de erro se for maior
   */
  public static function maxLength( $value, int $size, string $field_name = "campo" )
  {
    if ( strlen( $value ) > $size ) {
      throw new \Exception("O campo {$field_name} deve conter no maximo {$size} caracteres");
    }else{
      return $value; 
    }
  }

}