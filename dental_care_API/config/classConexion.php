<?php

namespace gtc_core;


class classConexion
{

    private $hostname = "localhost";
    private $username = "root";
    private $password = "1234";
    private $dbName = "dbname";
    private $names = "utf8";

    public $last_id;
    public $error;
    public $db_name;

    private function Conectar()
    {
        $conn = mysqli_connect($this->hostname,$this->username,$this->password);
        if(!$conn)
        {
            $Funciones = new Functions();
            die($Funciones->MensajeError("NO SE HA ESTABLECIDO CONEXION CON LA BASE DE DATOS."));
        }else
        {
            return $conn;
        }
    }

    public function Consulta($csql)
    {
        $conexion = $this->Conectar();
        if(!mysqli_select_db($conexion, $this->dbName))
        {
            $Funciones = new Functions();
            die($Funciones->MensajeError("NO SE ENCUENTRA LA BASE DE DATOS."));
        }else
        {
            if(!mysqli_query($conexion, "SET NAMES '".$this->names."'"))
            {
                $Funciones = new Functions();
                die($Funciones->MensajeError("ERROR CAMBIANDO LA CODIFICACION."));
            }else
            {
                $result = mysqli_query($conexion, $csql);
                if(!$result)
                {
                    $this->error = mysqli_error($conexion);
                    return false;
                }else
                {
                    $this->error = "Query OK";
                    $this->last_id = mysqli_insert_id($conexion);
                    return $result;
                }
            }
        }
    }

    /*******************************************************************************************/



}