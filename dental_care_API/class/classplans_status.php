<?php
namespace gtc_core;

use stdClass;

class classplans_status{
    public $status;
    public $id_plans_status = 0;
    public $_user_umo;
    public $_date_umo;

    function insert($plans_status,$id_user) {
        $conn = new classConexion();
        $sql = "CALL CRUD_plans_status(
            '".$plans_status->status."',
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
        $sql = "CALL CRUD_plans_status(
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
        $sql = "CALL CRUD_plans_status(
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
        $sql = "CALL CRUD_plans_status(
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
            $sql = "CALL CRUD_plans_status(
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

    function update($plans_status,$id_user){ 
        $conn = new classConexion();  
        $sql = "CALL CRUD_plans_status(
            '".$plans_status->status."',
            '".$id_user."',
            '".$plans_status->id_plans_status."',
            '',
            '3'
        )"; 
        $conn->Consulta($sql); 
        $last_id = $plans_status->id_plans_status;
        return true;
    } 

}
?>
