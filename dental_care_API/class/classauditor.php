<?php
namespace gtc_core;

use stdClass;

class classauditor{
    public $transaction;
    public $contract;
    public $premium;
    public $group_rate;
    public $dentalCare_fee;
    public $underwriter_cost;
    public $affiliate;
    public $dentalCare_net;
    public $transaction_date;
    public $payee_affiliate_name;
    public $payee_affiliate_id;
    public $payee_group_name;
    public $is_payed_group;
    public $group_check_number;
    public $group_payment_date;
    public $payee_group_id;
    public $payee_underwriter_name;
    public $is_payed_underwriter;
    public $payee_underwriter_check_number;
    public $payee_underwriter_payment_date;
    public $payee_underwriter_id;
    public $is_cancelled;
    public $is_payed_affiliate;
    public $doctor_name;
    public $doctor_id;
    public $plan_name;
    public $plan_id;
    public $facility_name;
    public $facility_id;
    public $patient_name;
    public $coordinator_name;
    public $coordinator_id;
    public $procedure_date;
    public $payment_method;
    public $a1_affiliate_name;
    public $a1_affiliate_is_payed;
    public $a1_check_number;
    public $a1_payment_date;
    public $a1_affiliate_id;
    public $a1_affiliate_amount;
    public $a2_affiliate_name;
    public $a2_affiliate_is_payed;
    public $a2_check_number;
    public $a2_payment_date;
    public $a2_affiliate_id;
    public $a2_affiliate_amount;
    public $a3_affiliate_name;
    public $a3_affiliate_is_payed;
    public $a3_check_number;
    public $a3_payment_date;
    public $a3_affiliate_id;
    public $a3_affiliate_amount;
    public $contract_status;
    public $transaction_fee;
    public $pay_by;
    public $patient_email;
    public $patient_phone;
    public $patient_full_address;
    public $purchase_date;
    public $completion_date;
    public $cancelation_date;
    public $id_auditor = 0;
    public $_user_umo;
    public $_date_umo;

    function insert($auditor,$id_user) {
        $conn = new classConexion();
        $sql = "CALL CRUD_auditor(
            '".$auditor->transaction."',
            '".$auditor->contract."',
            '".$auditor->premium."',
            '".$auditor->group_rate."',
            '".$auditor->dentalCare_fee."',
            '".$auditor->underwriter_cost."',
            '".$auditor->affiliate."',
            '".$auditor->dentalCare_net."',
            '".$auditor->transaction_date."',
            '".$auditor->payee_affiliate_name."',
            '".$auditor->payee_affiliate_id."',
            '".$auditor->payee_group_name."',
            '".$auditor->is_payed_group."',
            '".$auditor->group_check_number."',
            '".$auditor->group_payment_date."',
            '".$auditor->payee_group_id."',
            '".$auditor->payee_underwriter_name."',
            '".$auditor->is_payed_underwriter."',
            '".$auditor->payee_underwriter_check_number."',
            '".$auditor->payee_underwriter_payment_date."',
            '".$auditor->payee_underwriter_id."',
            '".$auditor->is_cancelled."',
            '".$auditor->is_payed_affiliate."',
            '".$auditor->doctor_name."',
            '".$auditor->doctor_id."',
            '".$auditor->plan_name."',
            '".$auditor->plan_id."',
            '".$auditor->facility_name."',
            '".$auditor->facility_id."',
            '".$auditor->patient_name."',
            '".$auditor->coordinator_name."',
            '".$auditor->coordinator_id."',
            '".$auditor->procedure_date."',
            '".$auditor->payment_method."',
            '".$auditor->a1_affiliate_name."',
            '".$auditor->a1_affiliate_is_payed."',
            '".$auditor->a1_check_number."',
            '".$auditor->a1_payment_date."',
            '".$auditor->a1_affiliate_id."',
            '".$auditor->a1_affiliate_amount."',
            '".$auditor->a2_affiliate_name."',
            '".$auditor->a2_affiliate_is_payed."',
            '".$auditor->a2_check_number."',
            '".$auditor->a2_payment_date."',
            '".$auditor->a2_affiliate_id."',
            '".$auditor->a2_affiliate_amount."',
            '".$auditor->a3_affiliate_name."',
            '".$auditor->a3_affiliate_is_payed."',
            '".$auditor->a3_check_number."',
            '".$auditor->a3_payment_date."',
            '".$auditor->a3_affiliate_id."',
            '".$auditor->a3_affiliate_amount."',
            '".$auditor->contract_status."',
            '".$auditor->transaction_fee."',
            '".$auditor->pay_by."',
            '".$auditor->patient_email."',
            '".$auditor->patient_phone."',
            '".$auditor->patient_full_address."',
            '".$auditor->purchase_date."',
            '".$auditor->completion_date."',
            '".$auditor->cancelation_date."',
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
        $sql = "CALL CRUD_auditor(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
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
        $sql = "CALL CRUD_auditor(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
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
        $sql = "CALL CRUD_auditor(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
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
            $sql = "CALL CRUD_auditor(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
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

    function update($auditor,$id_user){ 
        $conn = new classConexion();  
        $sql = "CALL CRUD_auditor(
            '".$auditor->transaction."',
            '".$auditor->contract."',
            '".$auditor->premium."',
            '".$auditor->group_rate."',
            '".$auditor->dentalCare_fee."',
            '".$auditor->underwriter_cost."',
            '".$auditor->affiliate."',
            '".$auditor->dentalCare_net."',
            '".$auditor->transaction_date."',
            '".$auditor->payee_affiliate_name."',
            '".$auditor->payee_affiliate_id."',
            '".$auditor->payee_group_name."',
            '".$auditor->is_payed_group."',
            '".$auditor->group_check_number."',
            '".$auditor->group_payment_date."',
            '".$auditor->payee_group_id."',
            '".$auditor->payee_underwriter_name."',
            '".$auditor->is_payed_underwriter."',
            '".$auditor->payee_underwriter_check_number."',
            '".$auditor->payee_underwriter_payment_date."',
            '".$auditor->payee_underwriter_id."',
            '".$auditor->is_cancelled."',
            '".$auditor->is_payed_affiliate."',
            '".$auditor->doctor_name."',
            '".$auditor->doctor_id."',
            '".$auditor->plan_name."',
            '".$auditor->plan_id."',
            '".$auditor->facility_name."',
            '".$auditor->facility_id."',
            '".$auditor->patient_name."',
            '".$auditor->coordinator_name."',
            '".$auditor->coordinator_id."',
            '".$auditor->procedure_date."',
            '".$auditor->payment_method."',
            '".$auditor->a1_affiliate_name."',
            '".$auditor->a1_affiliate_is_payed."',
            '".$auditor->a1_check_number."',
            '".$auditor->a1_payment_date."',
            '".$auditor->a1_affiliate_id."',
            '".$auditor->a1_affiliate_amount."',
            '".$auditor->a2_affiliate_name."',
            '".$auditor->a2_affiliate_is_payed."',
            '".$auditor->a2_check_number."',
            '".$auditor->a2_payment_date."',
            '".$auditor->a2_affiliate_id."',
            '".$auditor->a2_affiliate_amount."',
            '".$auditor->a3_affiliate_name."',
            '".$auditor->a3_affiliate_is_payed."',
            '".$auditor->a3_check_number."',
            '".$auditor->a3_payment_date."',
            '".$auditor->a3_affiliate_id."',
            '".$auditor->a3_affiliate_amount."',
            '".$auditor->contract_status."',
            '".$auditor->transaction_fee."',
            '".$auditor->pay_by."',
            '".$auditor->patient_email."',
            '".$auditor->patient_phone."',
            '".$auditor->patient_full_address."',
            '".$auditor->purchase_date."',
            '".$auditor->completion_date."',
            '".$auditor->cancelation_date."',
            '".$id_user."',
            '".$auditor->id_auditor."',
            '',
            '3'
        )"; 
        $conn->Consulta($sql); 
        $last_id = $auditor->id_auditor;
        return true;
    } 

}
?>
