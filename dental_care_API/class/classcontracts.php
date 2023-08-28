<?php
namespace gtc_core;

use stdClass;

class classcontracts{
    public $contract;
    public $contract_status;
    public $is_cancelled;
    public $contract_date;
    public $full_name;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $surgery_date;
    public $doctor;
    public $facility;
    public $plan;
    public $premium;
    public $payment_method;
    public $card_number;
    public $card_valid_to;
    public $card_cvv;
    public $routing_number;
    public $account_number;
    public $pay_by;
    public $coordinator_name;
    public $group_name;
    public $id_contracts = 0;
    public $_user_umo;
    public $_date_umo;

    function insert($contracts,$id_user) {
        $conn = new classConexion();
        $sql = "CALL CRUD_contracts(
            '".$contracts->contract."',
            '".$contracts->contract_status."',
            '".$contracts->is_cancelled."',
            '".$contracts->contract_date."',
            '".$contracts->full_name."',
            '".$contracts->first_name."',
            '".$contracts->last_name."',
            '".$contracts->email."',
            '".$contracts->phone."',
            '".$contracts->address."',
            '".$contracts->city."',
            '".$contracts->state."',
            '".$contracts->zip_code."',
            '".$contracts->surgery_date."',
            '".$contracts->doctor."',
            '".$contracts->facility."',
            '".$contracts->plan."',
            '".$contracts->premium."',
            '".$contracts->payment_method."',
            '".$contracts->card_number."',
            '".$contracts->card_valid_to."',
            '".$contracts->card_cvv."',
            '".$contracts->routing_number."',
            '".$contracts->account_number."',
            '".$contracts->pay_by."',
            '".$contracts->coordinator_name."',
            '".$contracts->group_name."',
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
        $sql = "CALL CRUD_contracts(
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
        $sql = "CALL CRUD_contracts(
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
        $sql = "CALL CRUD_contracts(
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
            $sql = "CALL CRUD_contracts(
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

    function update($contracts,$id_user){ 
        $conn = new classConexion();  
        $sql = "CALL CRUD_contracts(
            '".$contracts->contract."',
            '".$contracts->contract_status."',
            '".$contracts->is_cancelled."',
            '".$contracts->contract_date."',
            '".$contracts->full_name."',
            '".$contracts->first_name."',
            '".$contracts->last_name."',
            '".$contracts->email."',
            '".$contracts->phone."',
            '".$contracts->address."',
            '".$contracts->city."',
            '".$contracts->state."',
            '".$contracts->zip_code."',
            '".$contracts->surgery_date."',
            '".$contracts->doctor."',
            '".$contracts->facility."',
            '".$contracts->plan."',
            '".$contracts->premium."',
            '".$contracts->payment_method."',
            '".$contracts->card_number."',
            '".$contracts->card_valid_to."',
            '".$contracts->card_cvv."',
            '".$contracts->routing_number."',
            '".$contracts->account_number."',
            '".$contracts->pay_by."',
            '".$contracts->coordinator_name."',
            '".$contracts->group_name."',
            '".$id_user."',
            '".$contracts->id_contracts."',
            '',
            '3'
        )"; 
        $conn->Consulta($sql); 
        $last_id = $contracts->id_contracts;
        return true;
    } 

}
?>
