<?php
/**
 * Created by PhpStorm.
 * User: genady
 * Date: 6/25/19
 * Time: 1:44 PM
 */

class HomeController
{
  public function indexAction(){
      require_once 'FormsModel.php';
      $form_model = new FormsModel();
      $page_data = array(
          'token'=>$this->setAjaxToken(),
          'formsList'=>$form_model->getFormsList(),

      );
      ob_start();
      require_once 'home.php';
      ob_flush();
  }
  public function setAjaxToken(){
      $token = hash('sha256',microtime(),false);
      if(!isset($_SESSION['ajax_token'])){
          $_SESSION['ajax_token'] = $token;
      }

      return $token;
  }
}