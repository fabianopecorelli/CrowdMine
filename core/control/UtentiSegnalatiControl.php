<?php
/**
 * Created by PhpStorm.
 * User: Lino
 * Date: 04/12/2016
 * Time: 16:13
 */


include_once MODEL_DIR . 'Utente.php';
include_once MANAGER_DIR . 'UtenteManager.php';

$userManager = new UtenteManager();

$usersReported = $userManager->getReportedUtente();

include VIEW_DIR."/visualizzaUtentiSegnalati.php";
