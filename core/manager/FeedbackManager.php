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
include_once MODEL_DIR . 'StatisticheProfiloUtenteListObject.php';
include_once MANAGER_DIR.'Manager.php';

/**
 * Class FeedbackManager
 * This Class provides the business logic for the Feedback Management and methods for database access.
 */
class FeedbackManager extends Manager
{
    /**
     * FeedbackManager constructor.
     */
    public function __construct()
    {
    }

    public function insertFeedback($id=null,$idUtente,$idAnnuncio,$idValutato,$valutazione,$corpo,$data,$stato,$titolo){
        $INSERT_FEEDBACK = "INSERT INTO `feedback`
    (`id`, `id_utente`, `id_annuncio`, `id_valutato`, `valutazione`, `corpo`, `data`, `stato`, `titolo`)
     VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s')";

        $query = sprintf($INSERT_FEEDBACK,$id,$idUtente,$idAnnuncio,$idValutato,$valutazione,$corpo,$data,$stato,$titolo);

        if (!Manager::getDB()->query($query)) {
            header("Location: ". DOMINIO_SITO ); //add tosat notification
            throw new ApplicationException(ErrorUtils::$INSERIMENTO_FALLITO, Manager::getDB()->error, Manager::getDB()->errno);
        }
    }

    public function createFeedback($id=null,$idUtente,$idAnnuncio,$idValutato,$valutazione,$corpo,$data,$stato,$titolo){
        return new Feedback($id, $idAnnuncio, $idUtente, $idValutato, $corpo, $data, $stato, $valutazione, $titolo);
    }



    public function checkCollaboration($idVotante,$idAnnuncioVotato){
        $GET_CANDIDATURA = "SELECT candidatura.richiesta_inviata, candidatura.richiesta_accettata
            FROM    candidatura
            WHERE   candidatura.id_utente = $idVotante AND candidatura.id_annuncio = $idAnnuncioVotato";
        $resSet = self::getDB()->query($GET_CANDIDATURA);
        if(!$resSet){
            header("Location: ". DOMINIO_SITO ); //add tosat notification
            throw new ApplicationException(ErrorUtils::$INSERIMENTO_FALLITO, Manager::getDB()->error, Manager::getDB()->errno);
        }
        if(mysqli_num_rows($resSet) == 0)
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


    public function getFeedbackById($id){
        $GET_FEEDBACK_BY_NAME="SELECT feedback.* FROM feedback WHERE feedback.id=$id";
        $resSet = self::getDB()->query($GET_FEEDBACK_BY_NAME);
        if(!$resSet){
            $obj = mysqli_fetch_assoc($resSet);
            $f = new Feedback($obj['id'], $obj['id_annuncio'], $obj['id_utente'], $obj['id_valutato'], $obj['corpo'], $obj['data'], $obj['stato'], $obj['valutazione'], $obj['titolo']);
        }
        return $f;
    }

    public function setStatus($id,$stato){
        $UPDATE_STATUS="UPDATE feedback SET stato=$stato WHERE feedback.id=$id";
        $resSet = self::getDB()->query($UPDATE_STATUS);
        if($resSet)
            return true;
        else
            return false;
    }


    public function getListaFeedback($idUtente){
        $GET_FEEDBACK_BY_USER = "SELECT feedback.id,feedback.titolo,feedback.corpo,
            feedback.valutazione,utente.nome,utente.cognome,utente.immagine_profilo 
            FROM feedback, utente WHERE feedback.id_valutato=$idUtente AND utente.id=$idUtente";

        $resSet = self::getDB()->query($GET_FEEDBACK_BY_USER);
        return $this->feedbackLOToArray($resSet);
    }


    public function getListaFeedbackByMicrocategoria($idUtente, $microCategoria){
        $mc = $microCategoria->getId();
        $GET_FEEDBACK_BY_USER_MICRO = "SELECT feedback.* FROM feedback 
        WHERE feedback.id_valutato=$idUtente AND feedback.id_annuncio IN  
        (SELECT annuncio.id 
          FROM annuncio,riferito 
          WHERE annuncio.id_utente = $idUtente AND riferito.id_microcategoria = $mc AND annuncio.id = riferito.id_annuncio)";
        return $this->feedbackToArray(self::getDB()->query($GET_FEEDBACK_BY_USER_MICRO));
    }


    public function sortListaFeedback($idUtente,$param){
        $GET_FEEDBACK_BY_USER_PARAM = "SELECT feedback.id,feedback.titolo,feedback.corpo,feedback.valutazione,utente.nome,utente.cognome,utente.immagine_profilo 
            FROM feedback, utente 
            WHERE feedback.id_valutato=$idUtente AND utente.id=$idUtente
            ORDER BY $param";

        $resSet = self::getDB()->query($GET_FEEDBACK_BY_USER_PARAM);
        return $this->feedbackLOToArray($resSet);
    }


    public function getFeedbackSegnalati(){
        $stato = SEGNALATO;
        $GET_REPORTED_FEEDBACK = "SELECT feedback.*, utente.nome, utente.cognome, utente.immagine_profilo 
                                  FROM feedback,utente 
                                  WHERE feedback.stato = $stato AND feedback.id_valutato = utente.id";
        return $this->feedbackToLOArray(self::getDB()->query($GET_REPORTED_FEEDBACK));
    }

    public function getFeedbackAdmin(){
        $stato = AMMINISTRATORE;
        $GET_REPORTED_FEEDBACK = "SELECT feedback.* utente.nome, utente.cognome, utente.immagine_profilo 
                                  FROM feedback,utente 
                                  WHERE feedback.stato = $stato AND feedback.id_valutato = utente.id";
        return $this->feedbackToLOArray(self::getDB()->query($GET_REPORTED_FEEDBACK));
    }

    public function removeFeedback($idFeedback){
        $stato = ELIMINATO;
        $SET_DELETE_FEEDBACK_STATUS = "UPDATE feedback SET feedback.stato = $stato WHERE feedback.id = $idFeedback ";
        $rs = self::getDB()->query($SET_DELETE_FEEDBACK_STATUS);
        if($rs)
            return true;
        else
            return false;
    }

    private function feedbackToArray($resSet){
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

    private function feedbackLOToArray($resSet){
        $us = array();
        if ($resSet) {
            while ($obj = $resSet->fetch_assoc()) {
                $u = new FeedbackListObject($obj['id'],$obj['titolo'],$obj['corpo'],$obj['nome'],$obj['cognome'],$obj['immagine_profilo'],$obj['valutazione']);
                $us[] = $u->jsonSerialize();
            }
        }
        return $us;
    }

    /**This function return for each Micro Categoria, that an user has used in his ads, the number of positive feedback.
     *
     * @param $idUtente
     *
     * @return $result
     *
     */
    private function getCountPositive($idUtente){
        $GET_COUNT_POSITIVE = " SELECT microcategoria.nome, COUNT(feedback.id) as Positivi
                                FROM feedback, annuncio, riferito, microcategoria
                                WHERE  feedback.id_valutato = $idUtente AND
                                        feedback.valutazione > 2.5 AND
                                        feedback.id_annuncio = annuncio.id AND
                                        riferito.id_annuncio = annuncio.id AND
                                        riferito.id_microcategoria = microcategoria.id
                                GROUP BY microcategoria.nome ";
        $result = self::getDB()->query($GET_COUNT_POSITIVE);

        return $result;
    }

    /**This function return for each Micro Categoria, that an user has used in his ads, the number of negative feedback.
     *
     * @param $idUtente
     *
     * @return $result
     *
     */
    private function getCountNegative($idUtente){
        $GET_COUNT_NEGATIVE = " SELECT microcategoria.nome, COUNT(feedback.id) as Negativi
                                FROM feedback, annuncio, riferito, microcategoria
                                WHERE  feedback.id_valutato = $idUtente AND
                                        feedback.valutazione <= 2.5 AND
                                        feedback.id_annuncio = annuncio.id AND
                                        riferito.id_annuncio = annuncio.id AND
                                        riferito.id_microcategoria = microcategoria.id
                                GROUP BY microcategoria.nome ";
        $result = self::getDB()->query($GET_COUNT_NEGATIVE);

        return $result;

    }

    /**This function return for each Micro Categoria, that an user has used in his ads, the number of negative feedback
     * and the number of positive feedback.
     *
     * @param $idUtente
     *
     * @return $resultTable
     *
     */
    public function getFeedbackMicroCategoriaStats($idUtente){

        $resultTable = array();

        $resultNegative = $this->getCountNegative($idUtente);

        while ($obj = $resultNegative->fetch_assoc()){
            $tmp = new StatisticheProfiloUtenteListObject($obj["nome"],0,$obj["Negativi"]);
            $resultTable[] = $tmp;
        }

        $resultPositive = $this->getCountPositive($idUtente);

        while ($obj = $resultPositive->fetch_assoc()){

            foreach ($resultTable as $resT){

                if($resT->getMicroCategoria() == $obj["nome"]){

                    $resT->setFeedbackPositivi($obj["Positivi"]);

                }else{
                    $tmp = new StatisticheProfiloUtenteListObject($obj["nome"],$obj["Positivi"],0);

                    $resultTable[] = $tmp;
                }
            }
        }

        foreach($resultNegative as $tmp){
            $resultTable[] = $tmp->jsonSerialize();
        }

        return $resultTable;

    }

    /**This function return the average of the all positive feedback used in the ads by an user.
     *
     * @param $idUtente
     *
     * @return $result
     *
     */
    private function getAveragePositiveFeedback($idUtente){

        $GET_AVERAGE_FEEDBACK_POSITIVE =" SELECT AVG(feedback.id) as AveragePositive
                                FROM feedback, annuncio, riferito, microcategoria
                                WHERE  feedback.id_valutato = $idUtente AND
                                        feedback.valutazione > 2.5 AND
                                        feedback.id_annuncio = annuncio.id AND
                                        riferito.id_annuncio = annuncio.id AND
                                        riferito.id_microcategoria = microcategoria.id";


        $result = self::getDB()->query($GET_AVERAGE_FEEDBACK_POSITIVE);

        return $result;
    }

    /**This function return the average of the all negative feedback used in the ads by an user.
     *
     * @param $idUtente
     *
     * @return $result
     *
     */
    private function getAverageNegativeFeedback($idUtente){

        $GET_AVERAGE_FEEDBACK_NEGATIVE =" SELECT AVG(feedback.id) as AverageNegative
                                FROM feedback, annuncio, riferito, microcategoria
                                WHERE  feedback.id_valutato = $idUtente AND
                                        feedback.valutazione <= 2.5 AND
                                        feedback.id_annuncio = annuncio.id AND
                                        riferito.id_annuncio = annuncio.id AND
                                        riferito.id_microcategoria = microcategoria.id";

        $result = self::getDB()->query($GET_AVERAGE_FEEDBACK_NEGATIVE);

        return $result;
    }

    /**This function return the average of the all negative feedback and the average of the all positive feedback used
     * in the ads by an user.
     *
     * @param $idUtente
     *
     * @return $result
     *
     */
    public function getAveragesOfFeedbacks($idUtente){

        $resPositive = $this->getAveragePositiveFeedback($idUtente);
        $resNegative = $this->getAverageNegativeFeedback($idUtente);

        $result = array();

        while($obj = $resNegative->fetch_assoc()){
            $result["negative"]= $obj["AverageNegative"] ;
        }

        while($obj = $resPositive->fetch_assoc()){
            $result["positive"]= $obj["AveragePositive"] ;
        }

        return $result;

    }


}