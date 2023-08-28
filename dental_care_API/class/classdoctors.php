<?php
namespace gtc_core;

use stdClass;

class classdoctors{
    public $status;
    public $first_name;
    public $last_name;
    public $full_name;
    public $facilities;
    public $specialties;
    public $phone;
    public $email;
    public $preferred_method_of_contact;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $group_detail;
    public $surgical_coordinators;
    public $doctor_fee;
    public $linked_user;
    public $notes;
    public $image;
    public $filemanagerlist;
    public $id_doctors = 0;
    public $_user_umo;
    public $_date_umo;

    function insert($doctors,$id_user) {
        $conn = new classConexion();
        $sql = "CALL CRUD_doctors(
            '".$doctors->status."',
            '".$doctors->first_name."',
            '".$doctors->last_name."',
            '".$doctors->full_name."',
            '".$doctors->phone."',
            '".$doctors->email."',
            '".$doctors->preferred_method_of_contact."',
            '".$doctors->address."',
            '".$doctors->city."',
            '".$doctors->state."',
            '".$doctors->zip_code."',
            '".$doctors->group_detail."',
            '".$doctors->doctor_fee."',
            '".$doctors->linked_user."',
            '".$doctors->notes."',
            '".$doctors->image."',
            '".$doctors->filemanagerlist."',
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
            if (count($doctors->facilities) > 0){
                foreach ($doctors->facilities as $item){
                    $sql = "CALL CRUD_doctors(
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
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
            if (count($doctors->specialties) > 0){
                foreach ($doctors->specialties as $item){
                    $sql = "CALL CRUD_doctors(
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
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
                    '13'
                    )"; 
                    $conn->Consulta($sql);
                }
            }
            if (count($doctors->surgical_coordinators) > 0){
                foreach ($doctors->surgical_coordinators as $item){
                    $sql = "CALL CRUD_doctors(
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
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
                    '14'
                    )"; 
                    $conn->Consulta($sql);
                }
            }
            return $last_id;
        }
    }

    function select_facilities_multiselect($id){ 
        $conn = new classConexion();  
        $sql = "CALL CRUD_doctors(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
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

    function select_specialties_multiselect($id){ 
        $conn = new classConexion();  
        $sql = "CALL CRUD_doctors(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
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
            '23'
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

    function select_surgical_coordinators_multiselect($id){ 
        $conn = new classConexion();  
        $sql = "CALL CRUD_doctors(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
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
            '24'
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
        $sql = "CALL CRUD_doctors(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
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
        $sql = "CALL CRUD_doctors(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
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
        $sql = "CALL CRUD_doctors(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
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
            $sql = "CALL CRUD_doctors(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
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

    function update($doctors,$id_user){ 
        $conn = new classConexion();  
        $sql = "CALL CRUD_doctors(
            '".$doctors->status."',
            '".$doctors->first_name."',
            '".$doctors->last_name."',
            '".$doctors->full_name."',
            '".$doctors->phone."',
            '".$doctors->email."',
            '".$doctors->preferred_method_of_contact."',
            '".$doctors->address."',
            '".$doctors->city."',
            '".$doctors->state."',
            '".$doctors->zip_code."',
            '".$doctors->group_detail."',
            '".$doctors->doctor_fee."',
            '".$doctors->linked_user."',
            '".$doctors->notes."',
            '".$doctors->image."',
            '".$doctors->filemanagerlist."',
            '".$id_user."',
            '".$doctors->id_doctors."',
            '',
            '3'
        )"; 
        $conn->Consulta($sql); 
        $last_id = $doctors->id_doctors;
        if (count($doctors->facilities) > 0){
            foreach ($doctors->facilities as $item){
                $sql = "CALL CRUD_doctors(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
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
        if (count($doctors->specialties) > 0){
            foreach ($doctors->specialties as $item){
                $sql = "CALL CRUD_doctors(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
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
                '13'
                )"; 
               $conn->Consulta($sql);
            }
        }
        if (count($doctors->surgical_coordinators) > 0){
            foreach ($doctors->surgical_coordinators as $item){
                $sql = "CALL CRUD_doctors(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
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
                '14'
                )"; 
               $conn->Consulta($sql);
            }
        }
        return true;
    } 

}
?>
