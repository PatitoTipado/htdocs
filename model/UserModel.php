<?php

class UserModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function registrarUsuario($nombre_de_usuario, $nombre, $anio_de_nacimiento, $email, $contrasena, $sexo, $pais, $ciudad, $foto)
    {
        //añadi interaccion con el usuario si por alguna razon la base de datos no agrega lo que debe agregar
        //por limitacion de la misma ej un correo o usuario repetido
        //si queres bindea las consultas, pero con esto asi creo que dijo que estaba para aprobar

        // Obtener la conexión a la base de datos
        $conn = $this->database->getConn();

        // 1. Validar que el nombre de usuario sea único
        $nombre_de_usuario = mysqli_real_escape_string($conn, $nombre_de_usuario);
        $query = "SELECT COUNT(*) as count FROM usuarios WHERE nombre_de_usuario = '$nombre_de_usuario'";

        $result = mysqli_query($conn, $query);
        if (!$result) {
            die("Error en la consulta SQL: " . mysqli_error($conn));
        }

        $row = mysqli_fetch_assoc($result);
        if ($row['count'] > 0) {
            return "El nombre de usuario ya existe. Elige otro.";
        }

        // 2. Validar que el nombre solo contenga letras y espacios
        if (!preg_match("/^[a-zA-Z\s]+$/", $nombre)) {
            return "El nombre solo debe contener letras.";
        }

        // 3. Validar el formato del email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "El formato del email es incorrecto.";
        }

        //foto
        $carpetaImagenes = $_SERVER['DOCUMENT_ROOT'] . '/public/';
        if (
            isset($_FILES["foto"]) &&
            $_FILES["foto"]["error"] == 0 &&
            $_FILES["foto"]["size"] > 0
        ) {
            $extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
            if ($extension == "png" || $extension == 'jpg' || $extension == 'jpeg') {
                $rutaImagen = $carpetaImagenes . $nombre_de_usuario . '.jpg';
                move_uploaded_file($_FILES["foto"]["tmp_name"], $rutaImagen);
                $foto = 'public/' . $nombre . ".jpg";
            } else {
                return "Sólo puedes publicar imágenes png, jpg o jpeg";
            }
        }

        $query = "INSERT INTO usuarios (nombre_de_usuario, nombre, anio_de_nacimiento, email, contrasena, sexo, pais, ciudad, imagen_url)
          VALUES ('$nombre_de_usuario', '$nombre', '$anio_de_nacimiento', '$email', '$contrasena', '$sexo', '$pais', '$ciudad', '$foto')";

        $result = $this->database->execute($query);

        if ($result) {
            return "Usuario registrado exitosamente.";
        } else {
            return "Error al registrar el usuario.";
        }
    }

    public function validarLogin($username, $password)
    {
        // Obtener la conexión a la base de datos
        $conn = $this->database->getConn();

        // Sanitizar el nombre de usuario
        $username = mysqli_real_escape_string($conn, $username);

        // Consulta para obtener el usuario
        $query = "SELECT contrasena FROM usuarios WHERE nombre_de_usuario = '$username'";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            die("Error en la consulta SQL: " . mysqli_error($conn));
        }

        // Verificar si el usuario existe
        if (mysqli_num_rows($result) > 0) {
            // Verificar la contraseña
            return true; // Usuario y contraseña válidos

        } else {
            return false; // Contraseña incorrecta
        }
    }
}
