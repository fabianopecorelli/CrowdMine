<?php

echo "Annuncio Commentato: ";
echo $message = $_POST['commento'];

$manager = new AnnuncioManager(); /* Declaration and initialization a manager variable */

$utente = unserialize($_SESSION['utente']); /* Declaration and initialization a user variable contain an unserialized version of a parameter who reference to user's info, given from session*/
$idUtente = $utente->getId(); /* Declaration and initialization a user variable contain the user id */
$idAnnuncio = unserialize($_SESSION['idAnnuncio']); /* Declaration and initialization a user variable contain an unserialized version of a parameter who reference to annuncio's info, given from session */

//$manager->commentAnnuncio($idAnnuncio, $idUtente, $message);

?>