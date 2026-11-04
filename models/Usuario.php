<?php

require_once __DIR__ . '/../config/conexion.php';

class Usuario extends  \Conectar{

    public function login(){
        $conectar= parent::Conexion();//para conectar a la base de datos//
        parent::set_names();
       if(isset($_POST["enviar"])){
            $correo = $_POST["usu_correo"];
            $pass = $_POST["usu_pass"];
            $rol = $_POST["rol_id"];
            if(empty($correo) and empty($pass)){
                header("Location:".\Conectar::ruta()."index.php?m=2");
                exit();
            }else{
                $sql = "SELECT * FROM tm_usuario WHERE usu_correo=? AND usu_pass=? AND rol_id=? AND est='1'";
                $stmt=$conectar->prepare($sql);
                $stmt->bindValue(1, $correo);
                $stmt->bindValue(2, $pass);
                $stmt->bindValue(3, $rol);
                $stmt->execute();
                $resultado = $stmt->fetch();
                if(is_array($resultado) and count($resultado)>0){ //si el resultado es un array y tiene mas de 0 registros todo esta correcto y redirige a home//
                    $_SESSION["usu_id"]=$resultado["usu_id"];
                    $_SESSION["usu_nombre"]=$resultado["usu_nombre"];
                    $_SESSION["usu_paterno"]=$resultado["usu_paterno"];
                    $_SESSION["usu_materno"]=$resultado["usu_materno"];
                    $_SESSION["rol_id"]=$resultado["rol_id"];
                    header("Location:".\Conectar::ruta()."view/home/");
                    exit();
                }else{
                    header("Location:".\Conectar::ruta()."index.php?m=1");
                    exit();
                }
            }
        }
    }
}