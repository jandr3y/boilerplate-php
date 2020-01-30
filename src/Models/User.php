<?php
namespace App\Models;

/**
 * Classe Entidade Usuário
 *
 * Modelo de usuário
 *
 * @package     App
 * @subpackage  Models
 * @author      Lucas Jandrey <lucas@jandrey.dev>
 */
class User extends Model {

  protected $id;
  protected $email;
  protected $realname;
  protected $password;
  protected $verify_email;
  protected $role;

  public static $timestamps = true;

  public static $source = "User";

  public static $table = "users";

  public static $primary = "id";

  public static $hidden = ["password"];

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Get the value of id
   */ 
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set the value of id
   *
   * @return  self
   */ 
  public function setId($id)
  {
    $this->id = $id;

    return $this;
  }

  /**
   * Get the value of name
   */ 
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set the value of name
   *
   * @return  self
   */ 
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get the value of password
   */ 
  public function getPassword()
  {
    return $this->password;
  }

  /**
   * Set the value of password
   *
   * @return  self
   */ 
  public function setPassword($password)
  {
    $this->password = $password;

    return $this;
  }

  /**
   * Get the value of role
   */ 
  public function getRole()
  {
    return $this->role;
  }

  /**
   * Set the value of role
   *
   * @return  self
   */ 
  public function setRole($role)
  {
    $this->role = $role;

    return $this;
  }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getRealname()
    {
        return $this->realname;
    }

    /**
     * @param mixed $realname
     */
    public function setRealname($realname)
    {
        $this->realname = $realname;
    }

    /**
     * @return mixed
     */
    public function getVerifyEmail()
    {
        return $this->verify_email;
    }

    /**
     * @param mixed $verify_email
     */
    public function setVerifyEmail($verify_email)
    {
        $this->verify_email = $verify_email;
    }
    protected $picture;

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }
}