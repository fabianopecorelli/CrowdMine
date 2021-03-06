<?php

include_once MODEL_DIR . "Notifica.php";
include_once UTILS_DIR . "NotificationParsing.php";
include_once MODEL_DIR . "Utente.php";

/**
 * Created by PhpStorm.
 * User: Andrea Sarto
 * Date: 30/11/2016
 * Time: 11.16
 */
class NotificaManager extends Manager implements SplObserver
{
    /**
     * NotificaManager constructor.
     */
    public function __construct(){

    }

    /**
     * Create a Notifica object not persistent
     *
     * @param $data
     * @param $tipo
     * @param $info
     * @param $letto
     * @param $id
     * @return Notifica, object
     */
    public function createNotifica($data, $tipo, $info, $letto, $id=null){
        return new Notifica($data, $tipo, $info, $letto, $id);
    }

    /**
     * Insert a Notifica object in the database
     *
     * @param $data
     * @param $tipo
     * @param $info
     * @param $letto
     * @return int $id
     * @throws ApplicationException
     */
    public function insertNotifica($data, $tipo, $letto, $info){
        $INSERT_NOTIFICA = "INSERT INTO `notifica`(`date`, `tipo`, `letto`, `info`) VALUES ('%s','%s', '%s', '%s')";
        $query = sprintf($INSERT_NOTIFICA, $data, $tipo, $letto, $info);
        self::getDB()->query($query);
        $id = self::getDB()->insert_id;
        return $id;
    }

    public function updateNotifica($notifica){
        $UPDATE_NOTIFICA = "UPDATE notifica SET letto = '%s' WHERE id = '%s'";
        $query = sprintf($UPDATE_NOTIFICA, $notifica->getLetto(), $notifica->getId());
        self::getDB()->query($query);
    }

    public function getNotificaById($idNotifica){
        $FIND_BY__ID = "SELECT * FROM notifica WHERE id = '%s'";
        $query = sprintf($FIND_BY__ID, $idNotifica);
        $result = self::getDB()->query($query);
        $notifica = null;
        while($r = $result->fetch_assoc()){
            $notifica = new Notifica($r['date'], $r['tipo'], $r['info'], $r['letto'], $r['id']);
        }return $notifica;
    }

    /**
     *Send to Dispatcher the list of users to whom refers the notification with id as $idNotifica
     *
     * @param $listaUtenti
     * @param $idNotifica
     * @throws ApplicationException
     */
    public function sendToDispatcher($listaUtenti, $idNotifica){
        $size = count($listaUtenti);
        for($i = 0; $i < $size; $i++) {
            $idDestinatario = $listaUtenti[$i];
            $INSERT_IN_DISPATCHER = "INSERT INTO `dispatcher_notifica`(`id_utente`, `id_notifica`) VALUES ('%s','%s')";
            $query = sprintf($INSERT_IN_DISPATCHER, $idDestinatario, $idNotifica);
            self::getDB()->query($query);
        }
    }

    /**
     *Return a  Notifiche object with 'id' as $idUtente
     *
     * @param Double $idNotifica
     *
     * @return  A Notifica object
     */
    public function getNotificaByUtente($user){
        $parser = new NotificationParsing();
        $listNotifica = array();
        $LOAD_NOTIFICHE = "SELECT notifica.* FROM notifica, dispatcher_notifica WHERE dispatcher_notifica.id_utente = '%s'";
        $query = sprintf($LOAD_NOTIFICHE, $user->getId());
        $result = self::getDB()->query($query);
        if ($result) {
            while($n = $result->fetch_assoc()){
                $notifica = new Notifica($n['date'], $n['tipo'], $n['info'], $n['letto'], $n['id']);
                $listNotifica[] = $notifica;
            }
        }
        return $parser->formattingNotify($listNotifica, $user->getRuolo());
    }

    /**
     *
     * @param Utente $user
     * @return Notifica
     * @internal param $idNotifica
     */
    public function getNotificaNotVisualized($user){
        $parser = new NotificationParsing();
        $listNotifica = array();
        $LOAD_NOTIFICHE = "SELECT * FROM notifica, dispatcher_notifica WHERE dispatcher_notifica.id_utente = '%s' AND notifica.letto = '0' AND dispatcher_notifica.id_notifica = notifica.id";
        $query = sprintf($LOAD_NOTIFICHE, $user->getId());

        $result = Manager::getDB()->query($query);
        if ($result) {
            while($n = $result->fetch_assoc()) {
                $notifica = new Notifica($n['date'], $n['tipo'], $n['info'], $n['letto'], $n['id']);
                $listNotifica[] = $notifica;
            }
        }
        return $parser->formattingNotify($listNotifica, $user->getRuolo());
    }

    /**
     * Receive update from subject
     * @link http://php.net/manual/en/splobserver.update.php
     * @param SplSubject $subject <p>
     * The <b>SplSubject</b> notifying the observer of an update.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function update(SplSubject $subject)
    {
        $wrapperNotifica = $subject->getWrapperNotifica();

        $tipoNotifica = $wrapperNotifica["tipo_notifica"];
        $destinatari = $wrapperNotifica["lista_destinatari"];

        $timestamp = new DateTime();
        $data = $timestamp->format("Y-m-d H:i:s");

        if ($tipoNotifica == tipoNotifica::INSERIMENTO) {

            $tipoOggetto = $wrapperNotifica[ElementiInfoNotifica::TIPO_OGGETTO];
            $idOggetto = $wrapperNotifica[ElementiInfoNotifica::ID_OGGETTO];
            $nomeOggetto = $wrapperNotifica[ElementiInfoNotifica::NOME_OGGETTO];

            $this->notifyInserimento($tipoNotifica, $idOggetto, $tipoOggetto, $nomeOggetto, $data, $destinatari);

        } else if ($tipoNotifica == tipoNotifica::RISOLUZIONE) {
            $tipoOggetto = $wrapperNotifica[ElementiInfoNotifica::TIPO_OGGETTO];
            $idOggetto = $wrapperNotifica[ElementiInfoNotifica::ID_OGGETTO];
            $nomeOggetto = $wrapperNotifica[ElementiInfoNotifica::NOME_OGGETTO];
            $esito = $wrapperNotifica[ElementiInfoNotifica::ESITO_OGGETTO];

            $this->notifyRisoluzione($tipoNotifica, $idOggetto, $tipoOggetto, $nomeOggetto, $esito, $data,$destinatari);

        } else if ($tipoNotifica == tipoNotifica::DECISIONE) {
            $tipoOggetto = $wrapperNotifica[ElementiInfoNotifica::TIPO_OGGETTO];
            $idOggetto = $wrapperNotifica[ElementiInfoNotifica::ID_OGGETTO];
            $nomeOggetto = $wrapperNotifica[ElementiInfoNotifica::NOME_OGGETTO];
            $tipo = $wrapperNotifica[ElementiInfoNotifica::TIPO_PER_DECISIONE];
            $esito = $wrapperNotifica[ElementiInfoNotifica::ESITO_OGGETTO];

            $this->notifyDecisione($tipoNotifica, $idOggetto, $tipoOggetto, $nomeOggetto, $tipo, $esito, $data, $destinatari);

        } else if ($tipoNotifica == tipoNotifica::SEGNALAZIONE) {
            $tipoOggetto = $wrapperNotifica[ElementiInfoNotifica::TIPO_OGGETTO];
            $idOggetto = $wrapperNotifica[ElementiInfoNotifica::ID_OGGETTO];
            $nomeOggetto = $wrapperNotifica[ElementiInfoNotifica::NOME_OGGETTO];

            $this->notifySegnalazione($tipoNotifica, $idOggetto, $tipoOggetto, $nomeOggetto, $data,$destinatari);
        }

    }
    
    /**
     * This function insert an 'insert-notify' after the observer receive an update from an other manager(subject).
     * @param $tipoNotifica
     * @param $idOggetto
     * @param %tipoOggetto
     * @param $nomeOggetto
     * @param $data
     * @param $destinatari
     */
    public function notifyInserimento($tipoNotifica, $idOggetto,$tipoOggetto,$nomeOggetto,$data,$destinatari){
        $arrayJson = array(ElementiInfoNotifica::ID_OGGETTO => $idOggetto, ElementiInfoNotifica::TIPO_OGGETTO => $tipoOggetto, ElementiInfoNotifica::NOME_OGGETTO => $nomeOggetto);
        $info = json_encode($arrayJson);
        $idNotifica = $this->insertNotifica($data,$tipoNotifica,false, $info);
        $this->sendToDispatcher($destinatari, $idNotifica);
    }

    /**
     * This function insert an 'resolution-notify' after the observer receive an update from an other manager(subject).
     * @param $tipoNotifica
     * @param $idOggetto
     * @param $tipoOggetto
     * @param $nomeOggetto
     * @param $esito
     * @param $data
     * @param $destinatari
     */
    public function notifyRisoluzione($tipoNotifica, $idOggetto,$tipoOggetto,$nomeOggetto,$esito,$data,$destinatari){
        $arrayJson = array($idOggetto,$tipoOggetto,$esito,$nomeOggetto);
        $info = json_encode($arrayJson);
        $idNotifica = $this->insertNotifica($data,$tipoNotifica,false, $info);
        $this->sendToDispatcher($destinatari, $idNotifica);
    }

    /**
     * This function insert an 'decision-notify' after the observer receive an update from an other manager(subject).
     * @param $tipoNotifica
     * @param $idOggetto
     * @param $tipoOggetto
     * @param $nomeOggetto
     * @param $tipo
     * @param $esito
     * @param $data
     * @param $destinatari
     */
    public function notifyDecisione($tipoNotifica, $idOggetto,$tipoOggetto,$nomeOggetto,$tipo,$esito,$data,$destinatari){
        $arrayJson = array($idOggetto,$tipoOggetto,$tipo,$esito,$nomeOggetto);
        $info = json_encode($arrayJson);
        $idNotifica = $this->insertNotifica($data,$tipoNotifica,false, $info);
        $this->sendToDispatcher($destinatari, $idNotifica);
    }

    /**
     * This function insert an 'decision-notify' after the observer receive an update from an other manager(subject).
     * @param $tipoNotifica
     * @param $idOggetto
     * @param $tipoOggetto
     * @param $nomeOggetto
     * @param $data
     * @param $destinatari
     */
    public function notifySegnalazione($tipoNotifica, $idOggetto,$tipoOggetto,$nomeOggetto,$data,$destinatari){
        $arrayJson = array($idOggetto,$tipoOggetto,$nomeOggetto);
        $info = json_encode($arrayJson);
        $idNotifica = $this->insertNotifica($data,$tipoNotifica,false, $info);
        $this->sendToDispatcher($destinatari, $idNotifica);
    }

}