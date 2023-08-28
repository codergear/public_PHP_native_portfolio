<?php
namespace gtc_core;

use stdClass;

class classsurgical_coordinators{
    public $status;
    public $first_name;
    public $last_name;
    public $full_name;
    public $birthday;
    public $phone;
    public $email;
    public $preferred_method_of_contact;
    public $group_detail;
    public $group_manager;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $notes;
    public $picture;
    public $filemanagerlist;
    public $linked_user;
    public $id_surgical_coordinators = 0;
    public $_user_umo;
    public $_date_umo;

    function insert($surgical_coordinators,$id_user) {
        $conn = new classConexion();
        $sql = "CALL CRUD_surgical_coordinators(
            '".$surgical_coordinators->status."',
            '".$surgical_coordinators->first_name."',
            '".$surgical_coordinators->last_name."',
            '".$surgical_coordinators->full_name."',
            '".$surgical_coordinators->birthday."',
            '".$surgical_coordinators->phone."',
            '".$surgical_coordinators->email."',
            '".$surgical_coordinators->preferred_method_of_contact."',
            '".$surgical_coordinators->group_detail."',
            '".$surgical_coordinators->group_manager."',
            '".$surgical_coordinators->address."',
            '".$surgical_coordinators->city."',
            '".$surgical_coordinators->state."',
            '".$surgical_coordinators->zip_code."',
            '".$surgical_coordinators->notes."',
            '".$surgical_coordinators->picture."',
            '".$surgical_coordinators->filemanagerlist."',
            '".$surgical_coordinators->linked_user."',
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
        $sql = "CALL CRUD_surgical_coordinators(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
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
        $sql = "CALL CRUD_surgical_coordinators(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
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
        $sql = "CALL CRUD_surgical_coordinators(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
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
            $sql = "CALL CRUD_surgical_coordinators(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
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

    function update($surgical_coordinators,$id_user){ 
        $conn = new classConexion();  
        $sql = "CALL CRUD_surgical_coordinators(
            '".$surgical_coordinators->status."',
            '".$surgical_coordinators->first_name."',
            '".$surgical_coordinators->last_name."',
            '".$surgical_coordinators->full_name."',
            '".$surgical_coordinators->birthday."',
            '".$surgical_coordinators->phone."',
            '".$surgical_coordinators->email."',
            '".$surgical_coordinators->preferred_method_of_contact."',
            '".$surgical_coordinators->group_detail."',
            '".$surgical_coordinators->group_manager."',
            '".$surgical_coordinators->address."',
            '".$surgical_coordinators->city."',
            '".$surgical_coordinators->state."',
            '".$surgical_coordinators->zip_code."',
            '".$surgical_coordinators->notes."',
            '".$surgical_coordinators->picture."',
            '".$surgical_coordinators->filemanagerlist."',
            '".$surgical_coordinators->linked_user."',
            '".$id_user."',
            '".$surgical_coordinators->id_surgical_coordinators."',
            '',
            '3'
        )"; 
        $conn->Consulta($sql); 
        $last_id = $surgical_coordinators->id_surgical_coordinators;
        return true;
    } 

}
?>
