<?php

include_once MANAGER_DIR . 'AnnuncioManager.php';
include_once MANAGER_DIR . 'AnnuncioManager.php';
include_once CONTROL_DIR . "ControlUtils.php";
include_once EXCEPTION_DIR . "IllegalArgumentException.php";

include_once FILTER_DIR . 'SearchByStatusOR.php';
include_once FILTER_DIR . 'SearchByDateInterval.php';
include_once MODEL_DIR . "/Commento.php";
include_once CONTROL_DIR . "annuncioBaseControl.php";
include_once VIEW_DIR . "ViewUtils.php";
include_once CONTROL_DIR . "ControlUtils.php";
include_once FILTER_DIR . 'SearchByPreferedAds.php';

$userID = $user->getId();
$managerAnnuncio = new AnnuncioManager();

$filters= Array(
    new SearchByNotStatus(StatoAnnuncio::ELIMINATO),
    new SearchByNotStatus(StatoAnnuncio::DISATTIVATO),
    new SearchByNotStatus(StatoAnnuncio::AMMINISTRATORE),
    new SearchByNotStatus(StatoAnnuncio::REVISIONE),
    new SearchByNotStatus(StatoAnnuncio::REVISIONE_MODIFICA),
    new SearchByNotStatus(StatoAnnuncio::RICORSO),
    new SearchByPreferedAds($userID));

$base = new annuncioBaseControl($managerAnnuncio,$filters,false,true,true);

$annunci = $base->getAnnunci();
$listaUtenti = $base->getListaUtenti();
$listaCommenti = $base->getListaCommenti();
$listaCandidature = $base->getListaCandidature();
$listaMicro = $base->getListaMicro();
$AnnunciMicroRef = $base->getAnnunciMicroRef();

$sideBarIconName = "annunciPreferiti";

include_once VIEW_DIR . "home.php";

?>

