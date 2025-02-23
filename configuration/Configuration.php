<?php
include_once("helper/MysqlDatabase.php");
include_once("helper/IncludeFilePresenter.php");
include_once("helper/Router.php");
include_once("helper/MustachePresenter.php");
include_once ("helper/FileEmailSender.php");
require_once ('helper/FilePHPEmailSender.php');
include_once ('helper/PDFFilesGenerator.php');

include_once("model/UserModel.php");
include_once ("model/PartidaModel.php");
include_once ("model/RankingModel.php");
include_once ("model/PreguntaModel.php");
include_once ("model/AdminModel.php");
include_once ("model/ChatModel.php");

include_once("controller/LoginController.php");
include_once("controller/HomeController.php");
include_once("controller/RegistroController.php");
include_once("controller/PerfilController.php");
include_once("controller/PartidaController.php");
include_once ("controller/RankingController.php");
include_once ("controller/EditorController.php");
include_once ("controller/AdminController.php");
include_once ("controller/ReporteController.php");
include_once ("controller/ChatController.php");

include_once('vendor/Mustache/src/Mustache/Autoloader.php');
include_once('vendor/dompdf/autoload.inc.php');

class Configuration
{
    public function __construct() {}

    //CONTROLADOR

    public function getLoginController()
    {
        return new LoginController($this->getPresenter(), $this->getUserModel());
    }

    public function getRegistroController()
    {
        return new RegistroController($this->getPresenter(), $this->getUserModel());
    }

    public function getHomeController()
    {
        return new HomeController($this->getPresenter(), $this->getUserModel(),$this->getPartidaModel());
    }

    public function getPerfilController()
    {
        return new PerfilController($this->getPresenter(), $this->getUserModel(),$this->getPreguntaModel());
    }

    public function getPartidaController()
    {
        return new PartidaController($this->getPresenter(), $this->getPartidaModel());
    }

    public function getRankingController()
    {
        return new RankingController($this->getPresenter(), $this->getUserModel(), $this->getRankingModel());
    }

    public function getEditorController()
    {
        return new EditorController($this->getPresenter(), $this->getPreguntaModel());
    }

    public function getReporteController()
    {
        return new ReporteController($this->getPresenter(),$this->getPartidaModel());
    }

    public function getAdminController()
    {
        return new AdminController($this->getPresenter(),$this->getAdminModel(),$this->getPdfGenerator());
    }

    public function getChatController(){
        return new ChatController($this->getPresenter(),$this->getChatModel());
    }

    //MODELOS

    public function getRankingModel() {
        return new RankingModel($this->getDatabase());
    }

    public function getUserModel()
    {
        return new UserModel($this->getDatabase(),$this->getFileEmailSender(),$this->getPHPEmailSender());
    }

    public function getPartidaModel()
    {
        return new PartidaModel($this->getDatabase());
    }

    public function getPreguntaModel()
    {
        return new PreguntaModel($this->getDatabase());
    }

    private function getAdminModel()
    {
        return new AdminModel($this->getDatabase());
    }

    private function getChatModel()
    {
        return new ChatModel($this->getDatabase());
    }

    //HELPER

    public function getRouter()
    {
        return new Router($this, "getLoginController", "show");
    }

    private function getDatabase()
    {
        $config = parse_ini_file('configuration/config.ini');
        return new MysqlDatabase(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password'],
            $config["database"]
        );
    }

    private function getPHPEmailSender()
    {
        $config= parse_ini_file('configuration/emailConfig.ini');

        return new FilePHPEmailSender(
            $config['port'],
            $config['host'],
            $config['password'],
            $config['username']
        );
    }

    private function getPresenter()
    {
        return new MustachePresenter("./view");
    }

    private function getFileEmailSender()
    {
        return new FileEmailSender();
    }

    public function getPdfGenerator()
    {
        return new PDFFilesGenerator($this->getPresenter());
    }

}
