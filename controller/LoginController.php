<?php

class LoginController
{
    private $presenter;
    private $model;
    public function __construct( $presenter,$model)
    {
        $this->model=$model;
        $this->presenter = $presenter;
    }

    public function show()
    {
        $this->presenter->show('login',[]);
    }

    public function validarLogin()
    {
        if (isset($_POST["username"])&& isset($_POST["password"])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            header("location:/canciones");
            //pegarle al modelo para validar que el usuario sea correcto
            //si lo es redigir al home desde controler
            //si no recargar la pagina e imprimir contraseña o username incorrecto
        }

    }


}