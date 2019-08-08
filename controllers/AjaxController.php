<?php
/**
 * Created by PhpStorm.
 * User: genady
 * Date: 6/25/19
 * Time: 1:32 PM
 */

class AjaxController
{
    public function __construct()
    {
        require_once 'FormsModel.php';
        $this->forms_model = new FormsModel();
    }

    public function indexAction()
    {
        $get_data = json_decode($_POST['objectToSend'], true);
        if (isset($get_data['func']) && $get_data['func'] != '' && method_exists($this, $get_data['func'])) {
            $func_name = $get_data['func'];
            $get_data['func'] = NULL;

            $this->$func_name($get_data );
        }
    }
    private function getOneSimpleForm($form_container_data){
        $simple_form_data = $this->forms_model->getOneSimpleForm($form_container_data);
        if(is_array($simple_form_data)) {
            $form_data = $simple_form_data['simpleObject'];
            ob_start("ob_gzhandler");
            require_once 'simple_form_component.php';
            ob_end_flush();
        }else{
            if($form_container_data['simpleFormObj']['up']==1) {
                $form_container_data['parentAnswerCrypt'] = 0;
                $this->getOneQuestionForm($form_container_data);
            }
        }
    }
    private function getOneQuestionForm($form_container_data){
     $form_data = $this->forms_model->getOneQuestionForm($form_container_data);
     if($form_data =='0') {
         $form_container_data['up']='0';
         $simple_form_data = $this->forms_model->getOneSimpleForm($form_container_data);
         if(is_array($simple_form_data)) {
             $form_data = $simple_form_data['simpleObject'];
             ob_start("ob_gzhandler");
             require_once 'simple_form_component.php';
             ob_end_flush();
         }else{
             echo 0;
             return;
         }

     }else {
         $form_data['form_data'] = $form_container_data['questinnationCrypt'];
         ob_start("ob_gzhandler");
         require_once 'form_component_box.php';
         ob_end_flush();
     }

    }
    private function questionBack($form_container_data){
        $form_data = $this->forms_model->questionBack($form_container_data);
        if(isset($form_data['is_simple'])){
            $form_data['is_back'] = 1;
            ob_start("ob_gzhandler");
            require_once 'simple_form_component.php';
            ob_end_flush();
        }else {
            ob_start("ob_gzhandler");
            require_once 'form_component_box.php';
            ob_end_flush();
        }
    }
    private function simpleBack($object){
      $form_data = $this->forms_model->simpleBack($object);
      $form_data['is_back'] = 1;
        if(isset($form_data['is_simple'])){
            ob_start();
            require_once 'simple_form_component.php';
            ob_end_flush();
        }else{
            ob_start();
            require_once 'form_component_box.php';
            ob_end_flush();
        }
    }
    private function saveUserAnswersFormsObject($form_data){
        $res = $this->forms_model->saveUserAnswersFormsObject($form_data['userAnswersObjectForSave']);
        echo $res;
    }
    private function simpleFormSave($form_data){
        $param = $this->forms_model->saveSimpleForm($form_data['formObj']);
        if($param === true){
//            open next form func here ============================
        }
        var_dump($param);
    }

}