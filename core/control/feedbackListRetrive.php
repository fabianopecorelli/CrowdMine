<?php

/**
 * Created by PhpStorm.
 * User: Giovanni Leo
 * Date: 06/12/2016
 * Time: 22:29
 */

include_once MANAGER_DIR . "FeedbackManager.php";
include_once MANAGER_DIR . "UtenteManager.php";
include_once MODEL_DIR . "Utente.php";
include_once EXCEPTION_DIR . "IllegalArgumentException.php";
include_once MODEL_DIR . "FeedbackListObject.php";
include_once VIEW_DIR . "ViewUtils.php";
include_once MODEL_DIR . "Utente.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$feedbackManger = new FeedbackManager();
$userManager = new UtenteManager();

if (isset($_POST["id"])) {
$userID = $_POST["id"];
$feedbackListObjArray = array();
$feedbackListObjArray = setUserImageForFeedback($feedbackManger->getListaFeedback($userID));

echo json_encode($feedbackListObjArray);


} else {
    $return = array(
        'toastType' => "error",
        'toastMessage' => "Errore nella lista feedback ci scusiamo per il disagio"
    );
    echo json_encode($return);
}

}