<?php
/**
 * Created by PhpStorm.
 * User: Andrea Sarto
 * Date: 15/12/2016
 * Time: 08.53
 */

include_once MANAGER_DIR ."/AnnuncioManager.php";
include_once CONTROL_DIR."ControlUtils.php";

if($_SERVER["REQUEST_METHOD"]=="POST") {

    $manager = new AnnuncioManager();
    echo $idAnnuncio = $_POST['idAnnuncio'];
    $oldStatus=null;
    $stato='disattivato';
    $manager->updateStatus($idAnnuncio, $stato, $oldStatus);
    header("Location: ".getReferer(DOMINIO_SITO));
}