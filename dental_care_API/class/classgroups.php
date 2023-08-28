<?php
namespace gtc_core;

use stdClass;

class classgroups{
    public $status;
    public $agreement_date;
    public $entity_name;
    public $principal_name;
    public $principal_last_name;
    public $principal_title;
    public $principal_full_name;
    public $phone;
    public $email;
    public $group_rate;
    public $afiliate;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $pay_by;
    public $routing_number;
    public $account_number;
    public $cardpointe_token;
    public $linked_user;
    public $plan;
    public $notes;
    public $logo;
    public $filemanagerlist;
    public $id_groups = 0;
    public $_user_umo;
    public $_date_umo;

    function insert($groups,$id_user) {
        $conn = new classConexion();
        $sql = "CALL CRUD_groups_v2(
            '".$groups->status."',
            '".$groups->agreement_date."',
            '".$groups->entity_name."',
            '".$groups->principal_name."',
            '".$groups->principal_last_name."',
            '".$groups->principal_title."',
            '".$groups->principal_full_name."',
            '".$groups->phone."',
            '".$groups->email."',
            '".$groups->group_rate."',
            '".$groups->afiliate."',
            '".$groups->address."',
            '".$groups->city."',
            '".$groups->state."',
            '".$groups->zip_code."',
            '".$groups->pay_by."',
            '".$groups->routing_number."',
            '".$groups->account_number."',
            '".$groups->cardpointe_token."',
            '".$groups->linked_user."',
            '".$groups->notes."',
            '".$groups->logo."',
            '".$groups->filemanagerlist."',
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
            if (count($groups->plan) > 0){
                foreach ($groups->plan as $item){
                    $sql = "CALL CRUD_groups_v2(
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
                    '".$last_id."',
                    '".$item."',
                    '12'
                    )"; 
                    $conn->Consulta($sql);
                }
            }
            return $last_id;
        }
    }

    function select_plan_multiselect($id){ 
        $conn = new classConexion();  
        $sql = "CALL CRUD_groups_v2(
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
            '".$id."',
            '',
            '22'
        )"; 
        if (!$result = $conn->Consulta($sql)){ 
            return false; 
        } else {
            if (mysqli_num_rows($result) == 0){ return null ; }  
            while ($obj = $result -> fetch_object()) {
                $resultList[] = $obj;
            } 
            $result -> free_result(); 
            return $resultList;   
        }
    }

    function selectAll(){ 
        $conn = new classConexion();  
        $sql = "CALL CRUD_groups_v2(
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
        $sql = "CALL CRUD_groups_v2(
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
        $sql = "CALL CRUD_groups_v2(
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
            $sql = "CALL CRUD_groups_v2(
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

    function update($groups,$id_user){ 
        $conn = new classConexion();  
        $sql = "CALL CRUD_groups_v2(
            '".$groups->status."',
            '".$groups->agreement_date."',
            '".$groups->entity_name."',
            '".$groups->principal_name."',
            '".$groups->principal_last_name."',
            '".$groups->principal_title."',
            '".$groups->principal_full_name."',
            '".$groups->phone."',
            '".$groups->email."',
            '".$groups->group_rate."',
            '".$groups->afiliate."',
            '".$groups->address."',
            '".$groups->city."',
            '".$groups->state."',
            '".$groups->zip_code."',
            '".$groups->pay_by."',
            '".$groups->routing_number."',
            '".$groups->account_number."',
            '".$groups->cardpointe_token."',
            '".$groups->linked_user."',
            '".$groups->notes."',
            '".$groups->logo."',
            '".$groups->filemanagerlist."',
            '".$id_user."',
            '".$groups->id_groups."',
            '',
            '3'
        )"; 
        $conn->Consulta($sql); 
        $last_id = $groups->id_groups;
        if (count($groups->plan) > 0){
            foreach ($groups->plan as $item){
                $sql = "CALL CRUD_groups_v2(
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
                '".$last_id."',
                '".$item."',
                '12'
                )"; 
               $conn->Consulta($sql);
            }
        }
        return true;
    } 

}
?>
