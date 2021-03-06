<?php

/**
 * Created by PhpStorm.
 * User: LongSky
 * Date: 28/11/2016
 * Time: 11:43
 */

include_once MODEL_DIR . 'Feedback.php';
include_once MODEL_DIR . 'MicroCategoria.php';
include_once MODEL_DIR . 'FeedbackListObject.php';
include_once MODEL_DIR . 'Candidatura.php';

include_once MANAGER_DIR . 'Manager.php';
include_once MANAGER_DIR . 'UtenteManager.php';
include_once MANAGER_DIR . 'AnnuncioManager.php';
include_once MANAGER_DIR . 'NotificaManager.php';
include_once MODEL_DIR . 'StatisticheProfiloUtenteListObject.php';

/**
 * Class FeedbackManager
 * This Class provides the business logic for the Feedback Management and methods for database access.
 */
class FeedbackManager extends Manager implements SplSubject
{

    private $_observer;
    private $wrapperNotifica;

    /**
     * FeedbackManager constructor.
     */
    public function __construct()
    {
        $this->_observer = new SplObjectStorage();
        $notificaManager = new NotificaManager();
        $this->attach($notificaManager);
    }

    public function insertFeedback($id = null, $idUtente, $idAnnuncio, $idValutato, $valutazione, $corpo, $data, $stato, $titolo)
    {
        $INSERT_FEEDBACK = "INSERT INTO `feedback`
    (`id`, `id_utente`, `id_annuncio`, `id_valutato`, `valutazione`, `corpo`, `data`, `stato`, `titolo`)
     VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s')";

        $query = sprintf($INSERT_FEEDBACK, $id, $idUtente, $idAnnuncio, $idValutato, $valutazione, $corpo, $data, $stato, $titolo);

        if (!Manager::getDB()->query($query)) {
            header("Location: " . DOMINIO_SITO); //add tosat notification
            throw new ApplicationException(ErrorUtils::$INSERIMENTO_FALLITO, Manager::getDB()->error, Manager::getDB()->errno);
        }

        $annuncioManager = new AnnuncioManager();
        $annuncio = $annuncioManager->getAnnuncio($idAnnuncio);
        $this->inviaNotificaDiInserimento($idAnnuncio, "feedback", $annuncio->getTitolo(), array($annuncio->getIdUtente()));
    }

    public function createFeedback($id = null, $idUtente, $idAnnuncio, $idValutato, $valutazione, $corpo, $data, $stato, $titolo)
    {
        return new Feedback($id, $idAnnuncio, $idUtente, $idValutato, $corpo, $data, $stato, $valutazione, $titolo);
    }


    public function checkCollaboration($idVotante, $idAnnuncioVotato)
    {
        $GET_CANDIDATURA = "SELECT candidatura.richiesta_inviata, candidatura.richiesta_accettata
            FROM    candidatura
            WHERE   candidatura.id_utente = $idVotante AND candidatura.id_annuncio = $idAnnuncioVotato";
        $resSet = self::getDB()->query($GET_CANDIDATURA);
        if (!$resSet) {
            header("Location: " . DOMINIO_SITO); //add tosat notification
            throw new ApplicationException(ErrorUtils::$INSERIMENTO_FALLITO, Manager::getDB()->error, Manager::getDB()->errno);
        }
        if (mysqli_num_rows($resSet) == 0)
            return false;
        else {
            $row = mysqli_fetch_assoc($resSet);
            if (($row['richiesta_inviata'] != RichiestaInviataCandidatura::INVIATA) ||
                ($row['richiesta_accettata'] != RichiestaAccettataCandidatura::ACCETTATO)
            )
                return false;
            else
                return true;
        }
    }


    public function getFeedbackById($id)
    {
        $GET_FEEDBACK_BY_NAME = "SELECT feedback.* FROM feedback WHERE feedback.id=$id";
        $resSet = self::getDB()->query($GET_FEEDBACK_BY_NAME);

            $obj = mysqli_fetch_assoc($resSet);
            $f = new Feedback($obj['id'], $obj['id_annuncio'], $obj['id_utente'], $obj['id_valutato'], $obj['corpo'],
                $obj['data'], $obj['stato'], $obj['valutazione'], $obj['titolo']);

        return $f;
    }

    public function getFeedbackByIdLO($idFeedback, $IdUtenteValutato)
    {
        $feebackListObject = null;
        $GET_FEEDBACK_BY_ID = "SELECT feedback.id,feedback.titolo,feedback.corpo,
            feedback.valutazione,feedback.id_utente,utente.nome,utente.cognome,utente.immagine_profilo 
            FROM feedback, utente WHERE feedback.id_valutato=$IdUtenteValutato 
            AND utente.id=feedback.id_utente 
            AND feedback.id=$idFeedback
            AND ((feedback.stato='attivato')OR(feedback.stato='segnalato'))";
        $resSet = self::getDB()->query($GET_FEEDBACK_BY_ID);
        return $this->feedbackLOToArray($resSet);
        /*   if ($resSet) {
               while ($obj = $resSet->fetch_assoc()) {
                   $feebackListObject = new FeedbackListObject($obj['id'],$obj['titolo'],$obj['corpo'],
                       $obj['nome'],$obj['cognome'],
                       $obj['immagine_profilo'],$obj['valutazione']);
               }
           }
           return $feebackListObject;*/
    }

    public function setStatus($id, $stato)
    {
        $UPDATE_STATUS = "UPDATE feedback SET stato='$stato' WHERE feedback.id=$id";
        $resSet = self::getDB()->query($UPDATE_STATUS);
        /* if($resSet) {
             if($stato == SEGNALATO){
                 $annuncioManager = new AnnuncioManager();
                 $this->inviaNotificaDiSegnalazione($id,$annuncioManager->getAnnuncio($this->getFeedbackById($id)->getIdAnnuncio()));
             }
             return true;
         }
         else
             return false;*/
    }


    public function getListaFeedback($idUtente)
    {
        $GET_FEEDBACK_BY_USER = "SELECT feedback.id,feedback.titolo,feedback.corpo,
            feedback.valutazione,feedback.id_utente,utente.nome,utente.cognome,utente.immagine_profilo,utente.ruolo
            FROM feedback, utente WHERE feedback.id_valutato=$idUtente AND utente.id=feedback.id_utente
            AND feedback.stato='attivato'";

        $resSet = self::getDB()->query($GET_FEEDBACK_BY_USER);
        return $this->feedbackLOToArray($resSet);
    }


    public function getListaFeedbackByMicrocategoria($idUtente, $microCategoria)
    {
        $mc = $microCategoria->getId();
        $GET_FEEDBACK_BY_USER_MICRO = "SELECT feedback.* FROM feedback 
        WHERE feedback.id_valutato=$idUtente AND feedback.id_annuncio IN  
        (SELECT annuncio.id 
          FROM annuncio,riferito 
          WHERE annuncio.id_utente = $idUtente AND riferito.id_microcategoria = $mc AND annuncio.id = riferito.id_annuncio)";
        return $this->feedbackToArray(self::getDB()->query($GET_FEEDBACK_BY_USER_MICRO));
    }

    public function sortListaFeedback($idUtente,$param){
        $GET_FEEDBACK_BY_USER_PARAM = "SELECT feedback.id,feedback.titolo,feedback.corpo,feedback.valutazione,feedback.id_utente,utente.nome,utente.cognome,utente.immagine_profilo,utente.ruolo
            FROM feedback, utente 
            WHERE feedback.id_valutato=$idUtente AND utente.id=feedback.id_utente  AND (feedback.stato='attivato')
            ORDER BY $param DESC";

        $resSet = self::getDB()->query($GET_FEEDBACK_BY_USER_PARAM);
        return $this->feedbackLOToArray($resSet);
    }


    public function getFeedbackSegnalati()
    {
        $stato = SEGNALATO;
        $GET_REPORTED_FEEDBACK = "SELECT feedback.id,feedback.titolo,feedback.corpo,
            feedback.valutazione,feedback.id_utente, utente.nome, utente.cognome, utente.immagine_profilo,utente.ruolo
                                  FROM feedback,utente 
                                  WHERE feedback.stato ='$stato' AND feedback.id_utente = utente.id";
        $resSet = self::getDB()->query($GET_REPORTED_FEEDBACK);
        return $this->feedbackLOToArray($resSet);
    }

    public function getFeedbackAdmin()
    {
        $stato = AMMINISTRATORE;
        $segnalato = SEGNALATO;
        $GET_REPORTED_FEEDBACK = "SELECT feedback.id,feedback.titolo,feedback.corpo,
            feedback.valutazione,feedback.id_utente,utente.nome, utente.cognome, utente.immagine_profilo,utente.ruolo
                                  FROM feedback,utente 
                                  WHERE (feedback.stato = '$stato' OR feedback.stato = '$segnalato'  )AND feedback.id_utente = utente.id";
        return $this->feedbackLOToArray(self::getDB()->query($GET_REPORTED_FEEDBACK));
    }

    public function removeFeedback($idFeedback)
    {
        $stato = ELIMINATO;
        $SET_DELETE_FEEDBACK_STATUS = "UPDATE feedback SET feedback.stato = $stato WHERE feedback.id = $idFeedback ";
        $rs = self::getDB()->query($SET_DELETE_FEEDBACK_STATUS);
        if ($rs)
            return true;
        else
            return false;
    }

    private function feedbackToArray($resSet)
    {
        $feedback = array();
        if ($resSet) {
            while ($obj = $resSet->fetch_assoc()) {
                $data = new Date($obj['data']);
                $f = new Feedback($obj['id'], $obj['id_utente'], $obj['id_annuncio'], $obj['id_valutato'], $obj['valutazione'], $obj['corpo'], $data, $obj['stato'], $obj['titolo']);
                $feedback[] = $f;
            }
        }
        return $feedback;
    }

    private function feedbackLOToArray($resSet)
    {
        $us = array();
        if ($resSet) {
            while ($obj = $resSet->fetch_assoc()) {
                $u = new FeedbackListObject($obj['id'],
                    $obj['titolo'],
                    $obj['corpo'],
                    $obj['nome'],
                    $obj['cognome'],
                    $obj['immagine_profilo'],
                    $obj['valutazione'],
                    $obj['id_utente'],
                    $obj['ruolo']);
                $us[] = $u->jsonSerialize();
            }
        }
        return $us;
    }

    /**This function return for each Micro Categoria, that an user has used in his ads, the number of positive feedback.
     *
     * @param $idUtente
     *
     * @return array|bool
     *
     */
    private function getCountPositive($idUtente)
    {

        $GET_COUNT_POSITIVE = "SELECT microcategoria.nome, COUNT(feedback.id) as positivi
                                FROM feedback, annuncio, riferito, microcategoria
                                WHERE  feedback.id_valutato = '%s' AND
                                        feedback.valutazione > 2.5 AND
                                        feedback.id_annuncio = annuncio.id AND
                                        riferito.id_annuncio = annuncio.id AND
                                        riferito.id_microcategoria = microcategoria.id
                                GROUP BY microcategoria.nome";
        $query = sprintf($GET_COUNT_POSITIVE, $idUtente);
        $result = self::getDB()->query($query);

        if ($result) {
            $list = array();
            while ($r = $result->fetch_assoc()) {
                $listElement = new StatisticheProfiloUtenteListObject($r['nome'], $r['positivi'], " ");
                array_push($list, $listElement);
            }
            return $list;
        }
        return false;
    }

    /**This function return for each Micro Categoria, that an user has used in his ads, the number of negative feedback.
     *
     * @param $idUtente
     *
     * @return array|bool
     *
     */
    private function getCountNegative($idUtente)
    {

        $GET_COUNT_NEGATIVE = "SELECT microcategoria.nome, COUNT(feedback.id) as negativi
                                FROM feedback, annuncio, riferito, microcategoria
                                WHERE  feedback.id_valutato = '%s' AND
                                        feedback.valutazione <= 2.5 AND
                                        feedback.id_annuncio = annuncio.id AND
                                        riferito.id_annuncio = annuncio.id AND
                                        riferito.id_microcategoria = microcategoria.id
                                GROUP BY microcategoria.nome";
        $query = sprintf($GET_COUNT_NEGATIVE, $idUtente);
        $result = self::getDB()->query($query);

        if ($result) {
            $list = array();
            while ($r = $result->fetch_assoc()) {
                $listElement = new StatisticheProfiloUtenteListObject($r['nome'], " ", $r['negativi']);
                array_push($list, $listElement);
            }
            return $list;
        }
        return false;

    }

    /**This function return for each Micro Categoria, that an user has used in his ads, the number of negative feedback
     * and the number of positive feedback.
     *
     * @param $idUtente
     *
     * @return array|bool
     *
     */
    public function getFeedbackMicroCategoriaStats($idUtente)
    {

        $resultTable = array();

        $resultNegative = $this->getCountNegative($idUtente);

        $resultPositive = $this->getCountPositive($idUtente);

        if($resultPositive != false && $resultNegative != false) {

            $resultTable = array_merge($resultTable,$resultNegative);

            for ($i = 0; $i < sizeof($resultPositive); $i++) {

                for ($j = 0; $j < sizeof($resultTable); $j++) {

                    if ($resultPositive[$i]->getNome() == $resultTable[$j]->getNome()) {

                        $resultTable[$j]->setFeedbackPositivi($resultPositive[$i]->getFeedbackPositivi());

                    } else {

                        array_push($resultTable, $resultPositive[$i]);

                    }

                }
            }
        }else if($resultPositive==false && $resultNegative!=false){

            $result = array_merge($resultTable, $resultNegative);

        }else if($resultPositive!=false && $resultNegative==false){

            $result = array_merge($resultTable, $resultPositive);

        }else if($resultPositive==false && $resultNegative==false){
            return $result = array();
        }
        return $result;


    }

    /**This function return the average of the all positive feedback used in the ads by an user.
     *
     * @param $idUtente
     *
     * @return array|bool
     *
     */
    private function getAveragePositiveFeedback($idUtente)
    {

        $GET_AVERAGE_FEEDBACK_POSITIVE = "SELECT AVG(feedback.valutazione) as avgpositive
                                          FROM feedback,utente
                                          WHERE  feedback.id_valutato = '%s' AND feedback.id_utente = utente.id 
                                          AND feedback.valutazione > 2.5";

        $query = sprintf($GET_AVERAGE_FEEDBACK_POSITIVE, $idUtente);
        $result = self::getDB()->query($query);

        if ($result) {
            $r = $result->fetch_row();
            $list = array("avgpositive" => $r[0]);
            return $list;
        }
        return false;
    }

    /**This function return the average of the all negative feedback used in the ads by an user.
     *
     * @param $idUtente
     *
     * @return array|bool
     *
     */
    private function getAverageNegativeFeedback($idUtente)
    {

        $GET_AVERAGE_FEEDBACK_NEGATIVE = "SELECT AVG(feedback.valutazione) as avgnegative
                                          FROM feedback,utente
                                          WHERE  feedback.id_valutato = '%s' AND feedback.id_utente = utente.id 
                                          AND feedback.valutazione <= 2.5";

        $query = sprintf($GET_AVERAGE_FEEDBACK_NEGATIVE, $idUtente);
        $result = self::getDB()->query($query);

        if ($result) {
            $r = $result->fetch_row();
            $list = array("avgnegative" => $r[0]);
            return $list;
        }
        return false;
    }

    /**This function return the average of the all negative feedback and the average of the all positive feedback used
     * in the ads by an user.
     *
     * @param $idUtente
     *
     * @return array|bool
     *
     */
    public function getAveragesOfFeedbacks($idUtente)
    {
        $resPositive = $this->getAveragePositiveFeedback($idUtente);
        $resNegative = $this->getAverageNegativeFeedback($idUtente);


        if($resPositive!=false && $resNegative!=false) {

            return array_merge($resNegative, $resPositive);

        }else if($resPositive==false){

            return $resNegative;

        }else if($resNegative==false){

            return $resPositive;

        }
        return false;
    }

    /**
     * @param $id
     * @param Annuncio $annuncio
     */
    private function inviaNotificaDiInserimento($idOggetto, $tipoOggetto, $nome, $destinatari){
        $tipoNotifica = "inserimento";
        $this->setWrapperNotifica($idOggetto, $tipoOggetto, $tipoNotifica, $nome, $destinatari);
        $this->notify();
    }

    private function inviaNotificaDiSegnalazione($id, $annuncio){
        $tipo="segnalazione";
        $name= $annuncio->getTitolo();
        $utenteManager = new UtenteManager();
        $dest= $utenteManager->findUserOneInput(RuoloUtente::MODERATORE);
        $this->setWrapperNotifica($id, $tipo, $name, $dest);
        $this->notify();
    }

    public function setWrapperNotifica($idOggetto, $tipoOggetto, $tipoNotifica, $nome, $listaDestinatari = null){
        $this->wrapperNotifica = array(
            "id_oggetto" => $idOggetto,
            "tipo_oggetto" => $tipoOggetto,
            "tipo_notifica" => $tipoNotifica,
            "nome" => $nome,
            "lista_destinatari" => $listaDestinatari
        );
    }

    public function getWrapperNotifica(){
        return $this->wrapperNotifica;
    }

    public function attach(SplObserver $observer){
        $this->_observer->attach($observer);
    }

    public function detach(SplObserver $observer){
        $this->_observer->detach($observer);
    }

    public function notify(){
        foreach($this->_observer as $observer){
            $observer->update($this);
        }
    }

}