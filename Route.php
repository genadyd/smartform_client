<?php
/**
 * Created by PhpStorm.
 * User: genady
 * Date: 6/25/19
 * Time: 1:23 PM
 */

class Route
{
   public function __construct()
   {
       require_once 'controllers/HomeController.php';
       $home = new HomeController();
       $home->indexAction();
   }
}