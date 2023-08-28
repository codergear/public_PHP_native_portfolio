<?php
namespace gtc_core;

use stdClass;

class classsecurity_users{
    public $users_status;
    public $user;
    public $pass;
    public $full_name;
    public $origin;
    public $phone;
    public $email;
    public $role;
    public $photo;
    public $id_security_users = 0;
    public $_user_umo;
    public $_date_umo;

    function insert($security_users,$id_user) {
// PHP_class_insert extends
        $security_users->pass = password_hash($security_users->pass, PASSWORD_DEFAULT);
// PHP_class_insert extends

        $conn = new classConexion();
        $sql = "CALL CRUD_security_users(
            '".$security_users->users_status."',
            '".$security_users->user."',
            '".$security_users->pass."',
            '".$security_users->full_name."',
            '".$security_users->origin."',
            '".$security_users->phone."',
            '".$security_users->email."',
            '".$security_users->role."',
            '".$security_users->photo."',
            '".$id_user."',
            '0',
            '',
            '1'
        )"; 
        if (!$result = $conn->Consulta($sql)){ 
            return false; 
        } else {
            $last_id = $result->fetch_object()->last_id;
            $result->free_result();
            return $last_id;
        }
    }

    function selectAll(){ 
        $conn = new classConexion();  
        $sql = "CALL CRUD_security_users(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '0',
            '0',
            '',
            '2'
        )"; 
        if (!$result = $conn->Consulta($sql)){ 
            return false; 
        } else {
            if (mysqli_num_rows($result) == 0){return null;}
            while ($obj = $result -> fetch_object()) {
                $resultList[] = $obj;
            }
            $result->free_result();
            return $resultList;
        }
    }



    function selectBy($param_name, $param_value){ 
        $param = $param_name . " = \'" . $param_value ."\'" ;
        $conn = new classConexion();  
        $sql = "CALL CRUD_security_users(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '0',
            '0',
            '".$param."',
            '21'
        )"; 
        if (!$result = $conn->Consulta($sql)){ 
            return false; 
        } else  {
            if (mysqli_num_rows($result) == 0){ return null ; }  
            while ($obj = $result -> fetch_object()) {
                $resultList[] = $obj;
            } 
            $result -> free_result(); 
            return $resultList;   
        }
    } 

    function delete($id,$id_user){ 
        $QUERY_RESULT = new stdClass(); 
        $QUERY_RESULT->item_id = $id;  
        $QUERY_RESULT->error_flag = false; 
        $QUERY_RESULT->error_msg = ''; 
        $QUERY_RESULT->error_type = ''; 

        $conn = new classConexion();  
        $sql = "CALL CRUD_security_users(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '".$id_user."',
            '".$id."',
            '',
            '41'
        )"; 
        $result = $conn->Consulta($sql); 
        while ($obj = $result->fetch_object()) { 
            if($obj->count > 0){ 
                $QUERY_RESULT->error_flag = true; 
                $QUERY_RESULT->error_msg = 'The record can not be deleted, is used in module: ' . $obj->msg ; 
                $QUERY_RESULT->error_type = 'referential integrity'; 
            } 
        } 
        if ($QUERY_RESULT->error_flag){
        $result->free_result();   
        } else { 
            $sql = "CALL CRUD_security_users(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '".$id_user."',
                '".$id."',
                '',
                '4'
            )"; 
            $QUERY_RESULT->error_flag = !$conn->Consulta($sql);
        } 
        return $QUERY_RESULT; 
    } 

    function update($security_users,$id_user){ 
// PHP_class_update extends
        if(isset($security_users->_new_pass_flag)){
            if ($security_users->_new_pass_flag == true){
                $security_users->pass = password_hash($security_users->pass, PASSWORD_DEFAULT);
            }
        }
// PHP_class_update extends

        $conn = new classConexion();  
        $sql = "CALL CRUD_security_users(
            '".$security_users->users_status."',
            '".$security_users->user."',
            '".$security_users->pass."',
            '".$security_users->full_name."',
            '".$security_users->origin."',
            '".$security_users->phone."',
            '".$security_users->email."',
            '".$security_users->role."',
            '".$security_users->photo."',
            '".$id_user."',
            '".$security_users->id_security_users."',
            '',
            '3'
        )"; 
        $conn->Consulta($sql); 
        $last_id = $security_users->id_security_users;
        return true;
    } 

}
?>
