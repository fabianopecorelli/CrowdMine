<?php
/**
 * Created by PhpStorm.
 * User: Fabricio Nicolas Madaio
 * Date: 18/12/2016
 * Time: 23:44
 */
include_once MANAGER_DIR.'UtenteManager.php';
include_once MODEL_DIR.'MicroCategoria.php';
include_once CONTROL_DIR . "ControlUtils.php";
include_once MANAGER_DIR.'MicrocategoriaManager.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //$user = unserialize($_SESSION["utente"]);
    $userManager = new UtenteManager();
    $microManager = new MicrocategoriaManager();
    /**
     * Checking if the POST variable are septate
     */
    if(isset($_POST['macro'])){
        $idMacro = testInput($_POST['macro']);
    }else{
        $_SESSION['toast-type'] = "error";
        $_SESSION['toast-message'] = "Macro non settata";
        header("Location:" . DOMINIO_SITO . "/IndexMicrocategorie");
        throw new IllegalArgumentException("Macro non settata");
    }

    //micro creation
    $micro = new MicroCategoria($idMacro,testInput($_POST['newMicro']));
    $microId = $microManager->addMicrocategoria($micro);

    if($microId==0){
        $_SESSION['toast-type'] = "error";
        $_SESSION['toast-message'] = "Micro non valida";
        header("Location:" . DOMINIO_SITO . "/IndexMicrocategorie");
        throw new IllegalArgumentException("Micro non valida");
    }

    $micro->setId($microId);

    if($micro==false){
        $_SESSION['toast-type'] = "error";
        $_SESSION['toast-message'] = "Micro inesistente";
        header("Location:" . DOMINIO_SITO . "/IndexMicrocategorie");
        throw new IllegalArgumentException("Micro inesistente");
    }
    $userManager->addMicroCategoria($user, $micro);
    $_SESSION['toast-type'] = "success";
    $_SESSION['toast-message'] = "Microcategoria Aggiunta";
}

header("Location:" . DOMINIO_SITO . "/IndexMicrocategorie");