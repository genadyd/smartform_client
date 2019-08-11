<?php
/**
 * Created by PhpStorm.
 * User: Genady
 * Date: 02/07/2019
 * Time: 13:03
 */

class FormsModel
{
    private $_db;

    public function __construct()
    {
        require_once 'Db_Q.php';
        $db = new DbQ();
        $this->_db = $db->getConnection();
    }

    public function getFormsList()
    {
        $query = "SELECT crypt, form_name  FROM forms ";
        $st = $this->_db->query($query);
        if($st->rowCount()>0){
            return $st->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    public function getOneSimpleForm($form_data){
        if(!isset($form_data['simpleFormObj']['lastOrder'])) {
            $up = $form_data['up'];
            $next_order = false;
            $simples_query = "SELECT sf.crypt AS sf_crypt, up, sf.form_order, sf.title AS sf_title, sfe.* 
                              FROM simple_forms sf RIGHT JOIN simple_forms_elements sfe ON sf.crypt = sfe.form_crypt 
                              WHERE sf.smart_form_crypt = :SMART_FORM_CRYPT AND sf.form_order = 
                              (SELECT MIN(form_order) FROM simple_forms WHERE smart_form_crypt = :SMART_FORM_CRYPT AND up = :UP) AND up = :UP";
        }else{
            $up = $form_data['simpleFormObj']['up'];
//            $next_order = $form_data['simpleFormObj']['lastOrder']+1;
            $next_order_query  = "SELECT form_order FROM simple_forms WHERE form_order > :LAST_ORDER AND up = :UP LIMIT 1";
            $simples_query = " SELECT sf.crypt AS sf_crypt, up, sf.form_order, sf.title AS sf_title, sfe.* ";
            $simples_query .= " FROM simple_forms sf RIGHT JOIN simple_forms_elements sfe ON sf.crypt = sfe.form_crypt ";
            $simples_query .= " WHERE sf.smart_form_crypt = :SMART_FORM_CRYPT ";
            $simples_query .= " AND sf.form_order = ( SELECT form_order FROM simple_forms WHERE form_order > :LAST_ORDER AND up = :UP LIMIT 1 ) ";
            $simples_query .= " AND up = :UP ";
        }
        $st = $this->_db->prepare($simples_query);
        $st->bindParam(":SMART_FORM_CRYPT",$form_data['questinnationCrypt'] );
//        $st->bindParam(":NEXT_ORDER",$next_order );
        $st->bindParam(":LAST_ORDER",$form_data['simpleFormObj']['lastOrder'] );
        $st->bindParam(":UP",$up );
        $st->execute();
        if($st->rowCount()>0) {
            $forms_elements_object = $st->fetchAll(PDO::FETCH_ASSOC);
            $res = array();
            foreach ($forms_elements_object as $element) {
                $res['simpleObject']['title'] = $element['sf_title'];
                $res['simpleObject']['order'] = $element['form_order'];
                $res['simpleObject']['crypt'] = $element['sf_crypt'];
                $res['simpleObject']['up'] = $element['up'];
                $res['simpleObject']['questinnation_crypt'] = $form_data['questinnationCrypt'];
                $res['simpleObject']['fields'][$element['crypt']] = array(
                    'crypt' => $element['crypt'],
                    'type' => $element['type'],
                    'title' => $element['title'],
                    'placeholder' => $element['placeholder'],
                    'value' => $element['value']
                );
            }
        }else{
            $res = 0;
        }
        return $res;
    }
    public function getOneQuestionForm($form_data){
        $query = "SELECT * FROM questions WHERE form_crypt = :FORM_CRYPT AND answer_crypt = :PARENT_ANSWER";
        $quest_st = $this->_db->prepare($query);
        $form_crypt = htmlspecialchars($form_data['questinnationCrypt']);
        $parent_answer_crypt = htmlspecialchars($form_data['parentAnswerCrypt']);
        $quest_st->bindParam(":FORM_CRYPT",$form_crypt, PDO::PARAM_STR );
        $quest_st->bindParam(":PARENT_ANSWER",$parent_answer_crypt, PDO::PARAM_STR );
        $quest_st->execute();
        if($quest_st->rowCount()>0) {
            $res = $quest_st->fetch(PDO::FETCH_ASSOC);
            $res['answers'] = $this->getAnswersByQuestion($res['crypt']);
        }else{
            $res = '0';
        }
        return $res;
    }
    private function getAnswersByQuestion($question_crypt, $get_values = false){
        if(!$get_values) {
            $query = " SELECT ans.*, el.type, el.crypt as element_cript FROM answers ans JOIN elements el ON  ans.element_crypt = el.crypt WHERE ans.question_crypt = :QUESTION_CRYPT ";
            $ans_st = $this->_db->prepare($query);
            $ans_st->bindParam(":QUESTION_CRYPT", $question_crypt, PDO::PARAM_STR);
            $ans_st->execute();
            $ans_res = $ans_st->fetchAll(PDO::FETCH_ASSOC);
            $counter_array = array();
            foreach ($ans_res as $k => $ans) {
                if ($ans['type'] == 'radio') {
                    if (count($counter_array) == 0) {
                        $nam = 'rand';
                        array_push($counter_array, '1');
                    }
                    $ans_res[$k]['radio_name'] = $nam;
                }
            }
            return $ans_res;
        }else{
//            get question form structure ====================
            $query = " SELECT ans.*, el.type, el.crypt as element_cript 
                       FROM answers ans 
                       JOIN elements el ON  ans.element_crypt = el.crypt
                       WHERE ans.question_crypt = :QUESTION_CRYPT ";
            $ans_st = $this->_db->prepare($query);
            $ans_st->bindParam(":QUESTION_CRYPT", $question_crypt, PDO::PARAM_STR);
            $ans_st->execute();
            $ans_res = $ans_st->fetchAll(PDO::FETCH_ASSOC);
//            ======================================================================
//            get answers User Saved DAta=============================================
            $s_query = " SELECT answer_crypt FROM smart_user_answers WHERE session_crypt = :SESS AND question_crypt = :QUEST";
            $s_st = $this->_db->prepare($s_query);
            $s_st->bindParam(":QUEST", $question_crypt, PDO::PARAM_STR);
            $s_st->bindParam(":SESS", $_COOKIE['sess_c'], PDO::PARAM_STR);
            $s_st->execute();
            $row = $s_st->fetch(PDO::FETCH_ASSOC);
            $selected_answer = $row['answer_crypt'];
            $counter_array = array();
            foreach ($ans_res as $k => $ans) {
                if ($ans['type'] == 'radio') {
                    if (count($counter_array) == 0) {
                        $nam = 'rand';
                        array_push($counter_array, '1');
                    }
                    $ans_res[$k]['radio_name'] = $nam;
                    if($ans['crypt'] == $selected_answer ){
                        $ans_res[$k]['selected'] = '1';
                    }
                }
            }
            return $ans_res;
        }


    }
    public function saveUserAnswersFormsObject($answers_object){
        $ct = date('Y-m-d H:i:s', time());
        $form_crypt = $answers_object['formCrypt'];
        $us_ip = $_SERVER['REMOTE_ADDR'];
        $sess_crypt = $_COOKIE['sess_c'];
        $values = array();
        foreach($answers_object['questionAnswers'] as $quest => $ans ){
            $crypt = md5(microtime());
            $query = "INSERT INTO smart_user_answers (crypt, form_crypt, question_crypt, answer_crypt, session_crypt, CT) 
            VALUES ('".$crypt."', '".$form_crypt."', '".$quest."', '".$ans."', '".$sess_crypt."', '".$ct."')
            ON DUPLICATE KEY UPDATE answer_crypt = '".$ans."' ";
        }
        $st = $this->_db->query($query);
        return $st->errorCode();
    }

    private function createNewUser($phone_num){
//        check if user exists ========================================
        $ct = date('Y-m-d H:i:s', time());
        $crypt = md5(microtime());
        $query = "INSERT INTO smart_form_users (user_crypt, user_phone, CT, UT) VALUES(:CRYPT, :PHONE, :CT,:UT )";
        $st = $this->_db->prepare($query);
        $st->bindParam(":CRYPT", $crypt);
        $st->bindParam(":PHONE", $phone_num);
        $st->bindParam(":CT", $ct);
        $st->bindParam(":UT", $ct);
        $st->execute();
    }
    private function getOneUserByPhoneNum($phone_num){
        $check_query = "SELECT user_crypt FROM smart_form_users WHERE user_phone = :USER_PHONE";
        $check_st = $this->_db->prepare($check_query);
        $check_st->bindParam(":USER_PHONE", $phone_num);
        $check_st->execute();
        $row = $check_st->fetch(PDO::FETCH_ASSOC);
        return $row['user_crypt'];
    }
    private function newUserSessionOpen($user_crypt){
        $max_order = "SELECT MAX(session_order) FROM users_sessions WHERE user_crypt = :USER_CRYPT";
        $order = $max_order+1;
        $crypt = md5(microtime());
        $ct = date('Y-m-d H:i:s', time());
        $query = "INSERT INTO users_sessions (session_cript, user_crypt, session_end, session_order,  CT) VALUES(:CRYPT, :USER_CRYPT, :SESS_END, :SESS_ORDER, :CT)";
        $s_end = 0;
        $st = $this->_db->prepare($query);
        $st->bindParam(":CRYPT",$crypt );
        $st->bindParam(":USER_CRYPT",$user_crypt );
        $st->bindParam(":SESS_END",$s_end );
        $st->bindParam(":SESS_ORDER",$order );
        $st->bindParam(":CT",$ct );
        $st->execute();
        setcookie('sess_c',$crypt,time()+3600*24);/*Save session in cookie*/
        return $crypt;
    }
    private function simpleFormSaveData($save_data_object)
    {
        $values = array();
        foreach ($save_data_object['fields'] as $field_key => $field_val) {
            $ct = date('Y-m-d H:i:s', time());
            $query = " INSERT INTO simple_form_user_saved_data (smart_form_crypt, simple_form_crypt, element_crypt, saved_value, CT, UT, user_session_crypt) 
                      VALUES ('" . $save_data_object['questinnationCrypt'] . "', '" . $save_data_object['simpleFormCrypt'] . "', '" . $field_key . "',
                       '" . $field_val['value'] . "', '" . $ct . "', '" . $ct . "', '" . $save_data_object['sessionCrypt'] . "') ";
            $query .= " ON DUPLICATE KEY UPDATE saved_value = '" . $field_val['value'] . "', UT = '" . $ct . "' ";
            $st = $this->_db->query($query);
        }
    }
    private function ifSimpeFormIsFirst($simple_form_crypt, $questination_crypt){
        $query = "SELECT id FROM simple_forms WHERE crypt = :SIMPLE_CRYPT and form_order = (SELECT MIN(form_order) FROM simple_forms WHERE smart_form_crypt = :QUESTINNATION_CRYPT)";
        $st = $this->_db->prepare($query);
        $st->bindParam(":SIMPLE_CRYPT",$simple_form_crypt, PDO::PARAM_STR );
        $st->bindParam(":QUESTINNATION_CRYPT",$questination_crypt, PDO::PARAM_STR );
        $st->execute();
        $row = $st->rowCount();
        return $row>0?true:false;
    }
//    private function getMaxOrderSessionCryptByUser($user_crypt){
//        $query = "";
//    }
    public function saveSimpleForm($form_data){
        $form_crypt = $form_data['questinnationCrypt'];
        $simple_form_crypt = $form_data['simpleFormCrypt'];
        $if_first = $this->ifSimpeFormIsFirst($simple_form_crypt, $form_crypt);
        $phone = false;
        if($if_first){
            //        check if phone =========================
            foreach($form_data['fields'] as $key => $f){
                if($f['type']=='phone'){
                    $phone = true;
                    $phone_num = preg_replace('/[\D]/','',$f['value']);
                    break;
                }
            }
            if(!$phone){
                return 'err';
            }
            //        check if phone end =========================

            if(!isset($form_data['isBack'])|| $form_data['isBack']=='0' ) {
                $this->createNewUser($phone_num);/*craete new user*/
                $user_crypt = $this->getOneUserByPhoneNum($phone_num);
                $new_session_crypt = $this->newUserSessionOpen($user_crypt);
            }else{
                $new_session_crypt = $_COOKIE['sess_c'];
//                get_exists_user_id for update user phone (in "isBack" situation)
                $update_user_phone = "UPDATE smart_form_users SET user_phone = :PHONE WHERE user_crypt =( SELECT user_crypt FROM users_sessions WHERE session_cript = :SESS ) ";
                $st = $this->_db->prepare($update_user_phone);
                $st->bindParam(":PHONE",$phone_num );
                $st->bindParam(":SESS",$new_session_crypt );
                $st->execute();
//                var_dump($st->errorInfo());
            }

            $save_data_object = array(
                'questinnationCrypt'=>$form_data['questinnationCrypt'],
                'simpleFormCrypt' => $form_data['simpleFormCrypt'],
                'sessionCrypt'=> $new_session_crypt,
                'fields' =>$form_data['fields']
            );
        }else{
            $session_crypt = $_COOKIE['sess_c'];
            $save_data_object = array(
                'questinnationCrypt'=>$form_data['questinnationCrypt'],
                'simpleFormCrypt' => $form_data['simpleFormCrypt'],
                'sessionCrypt'=> $session_crypt,
                'fields' =>$form_data['fields']
            );

        }
        $save_res =  $this->simpleFormSaveData($save_data_object);
        return $save_res;
    }
    public function getOneQuestionByCrypt($form_crypt){
        $query = "SELECT * FROM questions WHERE crypt = :FORM_CRYPT ";
        $st =  $this->_db->prepare($query);
        $st->bindParam(":FORM_CRYPT",$form_crypt );
        $st->execute();
        $res = $st->fetch(PDO::FETCH_ASSOC);
        $answers =  $this->getAnswersByQuestion($form_crypt, true);
        $res['answers']= $answers;
        $res['form_data']= $res['form_crypt'];
        return $res;
    }
    public function getOneSimpleFormByCrypt($form_crypt, $get_saved_values = false){
        $query = "SELECT sfe.crypt AS element_crypt, sfe.title AS field_title, sfe.type, sfe.placeholder, value,  sf.* 
                  FROM simple_forms_elements sfe LEFT JOIN simple_forms sf ON sfe.form_crypt = sf.crypt   
                  WHERE sf.crypt = :FORM_CRYPT";
        $st = $this->_db->prepare($query);
        $st->bindParam(":FORM_CRYPT",$form_crypt );
        $st->execute();
        $res = $st->fetchAll(PDO::FETCH_ASSOC);
        if($get_saved_values){
            $values_array = array();
            $saved_values_query = "SELECT element_crypt, saved_value FROM simple_form_user_saved_data WHERE simple_form_crypt = :SIMPLE_FORM_CRYPT AND user_session_crypt = :SESS ";
            $val_st = $this->_db->prepare($saved_values_query);
            $val_st->bindParam(":SIMPLE_FORM_CRYPT",$form_crypt );
            $val_st->bindParam(":SESS",$_COOKIE['sess_c'] );
            $val_st->execute();
            if($val_st->rowCount()>0){
               $row = $val_st->fetchAll(PDO::FETCH_ASSOC);
               foreach ($row as $val){
                   $values_array[$val['element_crypt']] = $val['saved_value'];
               }
            }
        }
        $simple_form_object = array();
        foreach ($res as $element){
            $simple_form_object['crypt'] = $form_crypt;
            $simple_form_object['is_simple'] = 1;
            $simple_form_object['questinnation_crypt'] = $element['smart_form_crypt'];
            $simple_form_object['title'] = $element['title'];
            $simple_form_object['order'] = $element['form_order'];
            $simple_form_object['up'] = $element['up'];
            $simple_form_object['fields'][$element['element_crypt']] = array(
                'title'=>$element['field_title'],
                'type'=>$element['type'],
                'placeholder'=>$element['placeholder'],
            );
            if(isset($values_array[$element['element_crypt']])){
                $simple_form_object['fields'][$element['element_crypt']]['element_value'] =  $values_array[$element['element_crypt']];
            }
        }
        return $simple_form_object;
    }

    public function questionBack($data){
        $this->deleteSavedAnswerByQuestionId($data['questionCrypt']);
        if($data['parentAnswerCrypt']=='0'){
            $last_up_simple_query = "SELECT crypt FROM simple_forms WHERE up = 1 AND form_order = (SELECT MAX(form_order) FROM simple_forms WHERE up = 1)";
            $l_st = $this->_db->query($last_up_simple_query);
            $r = $l_st->fetch(PDO::FETCH_ASSOC);
            $crypt = $r['crypt'];
            return $this->getOneSimpleFormByCrypt($crypt, true);
        }
//        get parent answer Question =============
        $query = "SELECT * FROM questions WHERE crypt = (SELECT question_crypt from answers WHERE crypt = :PARENT_ANSWER_CRYPT) ";
       $st =  $this->_db->prepare($query);
       $st->bindParam(":PARENT_ANSWER_CRYPT",$data['parentAnswerCrypt'] );
       $st->execute();
        $res = $st->fetch(PDO::FETCH_ASSOC);
       $answers =  $this->getAnswersByQuestion($res['crypt'], true);
       $res['answers']= $answers;
       $res['form_data']= $res['form_crypt'];
        return $res;
    }
    public function simpleBack($data){
//        get previus simple form crypt ========================================
          $crypt_query = "SELECT crypt FROM simple_forms WHERE up = :UP AND form_order < :LAST_ORDER LIMIT 1";
          $st = $this->_db->prepare($crypt_query);
          $st->bindParam(":UP",$data['up'] );
          $st->bindParam(":LAST_ORDER",$data['simpleFormOrder'] );
          $st->execute();
          if($st->rowCount()>0) {
              $res = $st->fetch(PDO::FETCH_ASSOC);
              return $this->getOneSimpleFormByCrypt($res['crypt'], true);
          }else{
              $session = $_COOKIE['sess_c'];
              $query = " SELECT question_crypt FROM smart_user_answers WHERE session_crypt = :SESS AND  id = (SELECT MAX(id) FROM smart_user_answers WHERE session_crypt = :SESS)";
              $q_st = $this->_db->prepare($query);
              $q_st->bindParam(":SESS",$session );
              $q_st->execute();
//              return var_dump($q_st->errorInfo());
              $res = $q_st->fetch(PDO::FETCH_ASSOC);

              return $this->getOneQuestionByCrypt($res['question_crypt']);
          }
    }
    public function deleteSavedAnswerByQuestionId($question_crypt){
        $query = " DELETE FROM smart_user_answers WHERE question_crypt = :QUEST AND session_crypt = :SESS ";
        $st = $this->_db->prepare($query);
        $st->bindParam(":QUEST",$question_crypt, PDO::PARAM_STR );
        $st->bindParam(":SESS",$_COOKIE['sess_c'], PDO::PARAM_STR );
        $st->execute();
        return $st->rowCount();
    }



}