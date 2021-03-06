<?php
/**
 * Created by PhpStorm.
 * User: Lino
 * Date: 17/12/2016
 * Time: 18:50
 */

include_once MANAGER_DIR . "MacroCategoriaManager.php";
include_once MANAGER_DIR . "MicrocategoriaManager.php";
include_once MODEL_DIR . "MacroCategoria.php"; // questo model va tolto, serve solo per provare lo stub
include_once MANAGER_DIR ."AnnuncioManager.php";
include_once MANAGER_DIR ."UtenteManager.php";
include_once CONTROL_DIR ."ControlUtils.php";

if($_SERVER["REQUEST_METHOD"] == "POST") {

    if(!isset($_POST["nome"]) || !isset($_POST["idMacro"])) exit();

    if($_POST["nome"]=="micro"){
        $idMacro = testInput($_POST["idMacro"]);
        $microManager = new MicrocategoriaManager();

        $microList = $microManager->getMicrosByMacro($idMacro);
        $microUtenteList = $microManager->getUserMicros($user->getId());

            $toReturn = "";
            foreach ($microList as $micro) {

                $found = false;
                //search micro in microUtenteList
                foreach ($microUtenteList as $usermicro) {
                    if ($usermicro->getMicroCategoria()->getId() == $micro->getId()) {
                        $found = true;
                        break;
                    }
                }
                //if micro not found then print
                if($found==false)
                    $toReturn = $toReturn . "<option value=" . $micro->getId() . ">" . $micro->getNome() . "</option>";
            }

            if($toReturn=="")
                echo "<option value='' disabled selected>Non ci sono micro per questa macro.</option>";
            else
                echo $toReturn."<option value='' disabled selected>Seleziona la Microcategoria</option>";
    }

}
?>