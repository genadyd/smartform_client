<?php
/**
 * Created by PhpStorm.
 * User: Genady
 * Date: 02/07/2019
 * Time: 12:57
 */

class DbQ
{
    public function __construct()
    {
//        return $this->getConnection();
    }

    public function getConnection(){
      require_once 'smartform/config.php';
      $connect = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $connect->exec("set names utf8");
//      $connect = 'ggggg';
      return $connect;
  }
  public function aaa(){
        return 'aaa';
  }
}