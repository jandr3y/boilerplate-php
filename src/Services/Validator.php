<?php

namespace App\Services;

class Validator {

  public static function validate( $value, $alias = '' )
  {
    $validatorInterface = new ValidatorInterface( $value, $alias );
    return $validatorInterface;
  }

}

class ValidatorInterface {

  /**
   * @var mixed
   */
  private $value;

  /**
   * @var string
   */
  private $alias;

  public function __construct( $value , $alias )
  {
    $this->value = $value;
    $this->alias = $alias;
  }

  public function __call($name, $arguments)
  {
    if(method_exists($this, $name)) {
      if ( $name != 'required' ) {
        if ( ! isset( $this->value ) || empty( $this->value ) || trim( $this->value ) == '' ) {
          return $this;
        }else{
          return $this->$name( $this->alias );;
        }
      }
    }
  }

  /**
   * Valida se é uma senha forte
   *  - Deve conter caracteres especiais
   *  - Deve ter mais de 5 digitos
   * 
   * @param string $password | Valor
   * @return string Senha encriptada
   * @throws Exception Pode lançar uma exceção com a mensagem de erro
   */
  public function isStrongPassword()
  {
    if (strlen($this->value) >= 6){
      if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $this->value)){
        return $this;
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
  public function isUsername()
  {
    if ( preg_match('/^[a-z\d_]{4,20}$/i', $this->value) ) {
      return $this;
    }else{
      throw new \Exception("Nome de usuário deve conter apenas números e letras");
    }
  }

  /**
   * Valida se tem uma quantidade minima de caracteres
   * 
   * @param int $size tamanho minimo
   * @throws \Exception Mensagem de erro se for menor
   */
  public function minLength( int $size )
  {
    if ( strlen( $this->value ) <= $size ) {
      throw new \Exception("O campo {$this->alias} deve conter no mínimo {$size} caracteres");
    }else{
      return $this; 
    }
  }

  /**
   * Valida se tem uma quantidade maxima de caracteres
   * 
   * @param int $size tamanho maximo
   * @throws \Exception Mensagem de erro se for maior
   */
  public function maxLength( int $size )
  {
    if ( strlen( $this->value ) > $size ) {
      throw new \Exception("O campo {$this->alias} deve conter no maximo {$size} caracteres");
    }else{
      return $this;
    }
  }

  /**
   * Valida se a informação esta vazia
   */
  public function required()
  {
    if ( ! isset( $this->value ) || empty( $this->value ) || trim( $this->value ) == '' ) {
      throw new \Exception("O campo {$this->alias} não pode ficar vazio");
    }else{
      return $this;
    }
  }


  public function isEmail()
  {
    if( !filter_var($this->value, FILTER_VALIDATE_EMAIL) ) {
        throw new \Exception("O campo {$this->alias} deve ser um email valido");
    }else{
        return $this;
    }
  }

}