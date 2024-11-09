<?php

class UserModel
{
    private $database;
    private $emailSender;

    public function __construct($database, $emailSender)
    {
        $this->emailSender = $emailSender;
        $this->database = $database;
    }

    public function registrarUsuario($nombre_de_usuario, $nombre, $anio_de_nacimiento, $email, $contrasena, $foto, $sexo, $pais, $ciudad)
    {

        if ($this->validarNombreUsuario($nombre_de_usuario)) {
            return"el nombre de usuario elegido ya esta registrado.";
        }

        if ($this->validarContrasena($contrasena)) {
            return "la contraseña no cumple con la longuitud requerida.";
        }

        if ($this->validarQueSoloTengaCaracteres($nombre)) {
            return "el nombre debe tener solo letras y sin espacios.";
        }

        $fecha_registro = $this->obtenerFechaRegistro();

        $hash = $this->obtenerHash();

        $sql = "INSERT INTO usuarios 
        (nombre_de_usuario, nombre, anio_de_nacimiento, email, contrasena, sexo, pais, ciudad,fecha_registro,imagen_url,hash,rol) 
        VALUES 
        ('$nombre_de_usuario', '$nombre', '$anio_de_nacimiento', '$email', '$contrasena', '$sexo', '$pais', '$ciudad', '$fecha_registro','$foto','$hash','jugador')";

        if ($this->database->execute($sql)) {
            $this->emailSender->sendEmail($nombre_de_usuario, 'validacion correo', "tu codigo hash es '$hash'");
            return "exitoso";
        } else {
            return "ocurrio un error en la base de datos.";
        }
    }

    public function validarHash($hash)
    {
        $sql = "SELECT * FROM usuarios WHERE hash = '$hash'";
        $result = $this->database->execute($sql);

        if ($result->num_rows == 0) {
            return "codigo hash incorrecto";
        }

        $usuario = $result->fetch_assoc();

        if ($usuario['estado'] == 'inactivo') {

            $idUsuario = $usuario['id'];
            $updateQuery = "UPDATE usuarios SET estado = 'activo' WHERE id = $idUsuario";
            $this->database->execute($updateQuery);

            return false;
        } else {

            return "El usuario ya está activo.";
        }
    }

    public function validarLogin($usuario, $password)
    {
        $sql = "SELECT * FROM usuarios WHERE nombre_de_usuario = '$usuario' AND contrasena LIKE '$password' AND estado LIKE'activo'";

        $result = $this->database->execute($sql);

        if ($result->num_rows == 1) {

            $usuario = $result->fetch_assoc();

//            $_SESSION["user"] = $usuario["nombre_de_usuario"];
//            $_SESSION["email"] = $usuario["email"];
//            $_SESSION['foto'] = $usuario['imagen_url'];
//            $_SESSION['pais'] = $usuario['pais'];
//            $_SESSION['ciudad'] = $usuario['ciudad'];
//            $_SESSION['nombre'] = $usuario['nombre'];
//            $_SESSION['sexo'] = ($usuario['sexo'] == 'F') ? 'Femenino' : 'Masculino';

            $data['result'] = true;
            $data['id_usuario']=$usuario['id'];
            $data['rol']=$usuario['rol'];
            $data['user']=$usuario['nombre_de_usuario'];

            return $data;
        } else {
            $sql = "SELECT * FROM usuarios WHERE nombre_de_usuario = '$usuario' AND estado LIKE'activo'";

            $result = $this->database->execute($sql);
            $data['result']=false;
            $data['error']=($result->num_rows == 1) ? "contraseña incorrecta" : "usuario inexistente o inactivo";
            return $data;
        }
    }

    public function obtenerDatosDePerfil($id)
    {
        $sql = "SELECT * FROM usuarios WHERE id='$id'";

        $result = $this->database->execute($sql);

        if ($result->num_rows == 1) {

            $usuario = $result->fetch_assoc();

            $data['result'] = true;
            $data['foto']=$usuario['imagen_url'];
            $data['email']=$usuario['email'];
            $data['pais']=$usuario['pais'];
            $data['ciudad']= $usuario['ciudad'];
            $data['nombre']= $usuario['nombre'];
            $data['sexo']= ($usuario['sexo'] == 'F') ? 'Femenino' : 'Masculino';

            return $data;
        } else {

            $data['result']=false;
            $data['not_found']="no se encontro al usuario";
            return $data;
        }

    }

    private function validarNombreUsuario($nombre_de_usuario)
    {
        $sql = "SELECT * FROM usuarios WHERE nombre_de_usuario = '$nombre_de_usuario'";
        $result = $this->database->execute($sql);

        return $result->num_rows == 1;
    }

    private function validarContrasena($contrasena)
    {

        return strlen($contrasena) <= 8;
    }

    private function validarQueSoloTengaCaracteres($nombre)
    {
        return !(ctype_alpha($nombre));
    }

    private function obtenerHash()
    {
        $flag = false;

        while (!$flag) {

            $hash = rand(1, 1000);

            $sql = "SELECT * FROM usuarios WHERE hash='$hash'";
            $result = $this->database->execute($sql);

            if ($result->num_rows == 0) {
                $flag = true;
            }
        }

        return $hash;
    }

    private function obtenerFechaRegistro()
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $fecha_actual = new DateTime();

        return $fecha_actual->format('Y-m-d H:i:s');
    }

}
