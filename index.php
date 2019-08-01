<?php
/**
 * Created by PhpStorm.
 * User: Genady
 * Date: 14/07/2019
 * Time: 14:32
 */


session_start();
header('Content-type: text/html; charset=utf-8');
require_once 'loader.php';
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    /* special ajax here */
    require_once 'AjaxController.php';
    $aj = new AjaxController();
    $aj->indexAction();
}else{
    require_once 'Route.php';
    $r = new Route();
}