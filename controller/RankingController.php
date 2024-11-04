<?php

class RankingController
{

    private $presenter;
    private $model;
    private $rankingModel;

    public function __construct($presenter, $model, $rankingModel)
    {
        $this->model = $model;
        $this->presenter = $presenter;
        $this->rankingModel = $rankingModel;
    }

    public function show()
    {
        if (!isset($_SESSION['user'])) {
            header("location:/");
        }
        $data = $this->obtenerUsuarios();

        $dataCompleto = array_merge($data, $_SESSION);

        $this->presenter->show('ranking', $dataCompleto);
        unset($_SESSION['not_found']);
        unset($data['usuarios']);
    }

    private function obtenerUsuarios()
    {

        $data = $this->rankingModel->obtenerUsuarios();

        if (!$data['result']) {
            $_SESSION['not_found'] = "no se encontraron usuarios";
        }

        return $data;
    }

  private function verUsuario()
    {
        $id_usuario = $_GET['id'];
       
    }
}
