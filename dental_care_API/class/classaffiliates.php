<?php
namespace gtc_core;

use stdClass;

class classaffiliates{
    public $status;
    public $agreement_date;
    public $afiliate_level;
    public $manager_afiliate;
    public $affiliate_to;
    public $first_name;
    public $last_name;
    public $full_name;
    public $birth_date;
    public $phone;
    public $email;
    public $preferred_method_of_contact;
    public $occupation;
    public $company;
    public $commission;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $notes;
    public $picture;
    public $filemanagerlist;
    public $linked_user;
    public $id_affiliates = 0;
    public $_user_umo;
    public $_date_umo;

    function insert($affiliates,$id_user) {
        $conn = new classConexion();
        $sql = "CALL CRUD_affiliates(
            '".$affiliates->status."',
            '".$affiliates->agreement_date."',
            '".$affiliates->afiliate_level."',
            '".$affiliates->manager_afiliate."',
            '".$affiliates->affiliate_to."',
            '".$affiliates->first_name."',
            '".$affiliates->last_name."',
            '".$affiliates->full_name."',
            '".$affiliates->birth_date."',
            '".$affiliates->phone."',
            '".$affiliates->email."',
            '".$affiliates->preferred_method_of_contact."',
            '".$affiliates->occupation."',
            '".$affiliates->company."',
            '".$affiliates->commission."',
            '".$affiliates->address."',
            '".$affiliates->city."',
            '".$affiliates->state."',
            '".$affiliates->zip_code."',
            '".$affiliates->notes."',
            '".$affiliates->picture."',
            '".$affiliates->filemanagerlist."',
            '".$affiliates->linked_user."',
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
        $sql = "CALL CRUD_affiliates(
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
        $sql = "CALL CRUD_affiliates(
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
        $sql = "CALL CRUD_affiliates(
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
            $sql = "CALL CRUD_affiliates(
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

    function update($affiliates,$id_user){ 
        $conn = new classConexion();  
        $sql = "CALL CRUD_affiliates(
            '".$affiliates->status."',
            '".$affiliates->agreement_date."',
            '".$affiliates->afiliate_level."',
            '".$affiliates->manager_afiliate."',
            '".$affiliates->affiliate_to."',
            '".$affiliates->first_name."',
            '".$affiliates->last_name."',
            '".$affiliates->full_name."',
            '".$affiliates->birth_date."',
            '".$affiliates->phone."',
            '".$affiliates->email."',
            '".$affiliates->preferred_method_of_contact."',
            '".$affiliates->occupation."',
            '".$affiliates->company."',
            '".$affiliates->commission."',
            '".$affiliates->address."',
            '".$affiliates->city."',
            '".$affiliates->state."',
            '".$affiliates->zip_code."',
            '".$affiliates->notes."',
            '".$affiliates->picture."',
            '".$affiliates->filemanagerlist."',
            '".$affiliates->linked_user."',
            '".$id_user."',
            '".$affiliates->id_affiliates."',
            '',
            '3'
        )"; 
        $conn->Consulta($sql); 
        $last_id = $affiliates->id_affiliates;
        return true;
    } 

}
?>
