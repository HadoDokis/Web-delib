<?php
/**
 * Application: webdelib / Adullact.
 * Date: 14/01/14
 * @author: Florian Ajir <florian.ajir@adullact.org>
 * @license CeCiLL V2 <http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html>
 */
App::uses('AppModel', 'Model');
App::uses('Cron', 'Model');
class CronJob extends AppModel {
    public $useTable = false;

    /**
     * Mise à jour des dossiers en attente de signature dans le i-parapheur connecté
     * @return string
     */
    public function signatureJob() {
        App::uses('Signature', 'Lib');
        $this->Signature = new Signature;
        return $this->Signature->updateAll();
    }

    /**
     * Mise à jour des projets en délégation dans le i-parapheur connecté
     * @return string
     */
    public function delegationJob() {
        App::uses('Traitement', 'Cakeflow.Model');
        $this->Traitement = new Traitement();
        return $this->Traitement->majTraitementsParapheur();
    }

    /**
     * @return string
     */
    public function retardCakeflowJob() {
        try {
            $users = array();
            //Import des modèles
            App::uses('User', 'Model');
            $this->User = new User();
            App::uses('Deliberation', 'Model');
            $this->Deliberation = new Deliberation();
            App::uses('Traitement', 'Cakeflow.Model');
            $this->Traitement = new Traitement();
            $this->Traitement->Behaviors->load('Containable');
            $traitements = $this->Traitement->find('all', array(
                'contain' => array(
                    'Visa' => array(
                        'conditions' => array(
                            'not' => array('Visa.date_retard' => null),
                            'Visa.date_retard <=' => date('Y-m-d H:i:s'),
                            'Visa.action' => 'RI',
                        ),
                        'fields' => array('Visa.id', 'Visa.date_retard', 'Visa.numero_traitement', 'Visa.trigger_id', 'Visa.action'),
                    )
                ),
                'fields' => array('Traitement.id', 'Traitement.numero_traitement', 'Traitement.target_id'),
                'conditions' => array('treated' => false)
            ));
            $messages = array();
            foreach ($traitements as $traitement) {
                if (empty($traitement['Visa'])) continue;
                foreach ($traitement['Visa'] as $visa) {
                    // Si le visa ne respecte pas ces conditions, on passe au suivant
                    if (empty($visa['date_retard'])
                        || $visa['date_retard'] > date(Cron::FORMAT_DATE)
                        || $visa['action'] != 'RI'
                        || $traitement['Traitement']['numero_traitement'] != $visa['numero_traitement']
                    ) continue;
                    $messages[$visa['trigger_id']][] = $traitement['Traitement']['target_id'];
                }
            }
            foreach ($messages as $user => $targets){
                $blaze = $this->User->prenomNomLogin($user);
                if (!empty($blaze) && !in_array($blaze, $users))
                    $users[] = $blaze;
                //Envoi notification
                foreach ($targets as $target)
                    $this->User->notifier($target, $user, 'retard_validation');
            }

            if (empty($users))
                return Cron::MESSAGE_FIN_EXEC_SUCCES . "Aucun utilisateur en retard";
            else
                return Cron::MESSAGE_FIN_EXEC_SUCCES . "Utilisateurs alertés :\n\n" . implode(",\n", $users);
        } catch (Exception $e) {
            return Cron::MESSAGE_FIN_EXEC_ERROR . $e->getMessage();
        }
    }

    /**
     * Tâche planifiée mise à jour des mail Sec S2low
     */
    public function mailSecJob() {
        //Si service désactivé ==> quitter
        if (!Configure::read('USE_S2LOW')) {
            return Cron::MESSAGE_FIN_EXEC_ERROR . "\nService S2LOW désactivé";
        }
        try {
            //Initialisations
            App::uses('ComponentCollection', 'Controller');
            App::uses('S2lowComponent', 'Controller/Component');
            App::uses('Acteurseance', 'Model');
            $collection = new ComponentCollection();
            $this->S2low = new S2lowComponent($collection);
            $this->Acteurseance = new Acteurseance();

            $mails = $this->Acteurseance->find('all', array(
                'recursive' => -1,
// TODO: inclure dans le rapport : les acteurs concernés
//                'contain' => array('Acteur'),
                'conditions' => array(
                    'date_envoi !=' => null,
                    'date_reception' => null
                )));
            foreach ($mails as $mail) {
                $this->Acteurseance->id = $mail['Acteurseance']['id'];
                $mail_id = $mail['Acteurseance']['mail_id'];
                $infos = $this->S2low->checkMail($mail_id);
                $debut = strpos($infos, 'mailTo:t:');
                $tmp = substr($infos, $debut + strlen('mailTo:t:'), strlen($infos));
                $fin = strpos($tmp, '==message==');
                $info = trim(substr($tmp, 0, $fin));
                if ($debut === false)
                    continue;
                else {
                    $this->Acteurseance->saveField('date_reception', $info);
                }
            }
            return Cron::MESSAGE_FIN_EXEC_SUCCES;
        } catch (Exception $e) {
            return Cron::MESSAGE_FIN_EXEC_ERROR . $e->getMessage();
        }
    }
    
    /**
     * @return string
     */
    public function convertionAnnexesJob($delib_id=NULL, $refresh=false)
    {
        try {
            //Import des modèles
            App::uses('Annex', 'Model');
            App::uses('ComponentCollection', 'Controller');
            App::uses('ConversionComponent', 'Controller/Component');
            App::uses('ConversionComponent', 'Controller/Component');
            $collection = new ComponentCollection();
            $this->Conversion = new ConversionComponent($collection);
            
            $DOC_TYPE = Configure::read('DOC_TYPE');
            $this->Annex = new Annex();
            $condition=array();
               if(!empty($delib_id))
            $condition['foreign_key']=$delib_id;
            
            if($refresh)
            $condition['AND']=array('OR'=>array('joindre_ctrl_legalite'=>TRUE, 'joindre_fusion'=>1));
            else
            $condition['AND']=array('OR'=>array(    
                                                array('AND'=>array('joindre_ctrl_legalite'=>TRUE,'data_pdf'=>NULL)),
                                                array('AND'=>array('joindre_fusion'=>1,'edition_data'=>NULL))
                                                )
            );
   
            
            $annexes = $this->Annex->find('all', array(
                'fields' => array('id','data','filename','filetype','joindre_ctrl_legalite','joindre_fusion'),
                'conditions' => $condition,
                'limit'=>empty($delib_id)?10:20,//optimisation pour le cron 10 taches par passage
                'order'=>'modified DESC',
                'recursive'=>-1
            ));
           /* debug($condition);
            $log = $this->Annex->getDataSource()->getLog(false, false);
           // var_dump($log);exit;
            var_dump($annexes);
            exit;*/
            
            //if (!empty($annexes))
            foreach ($annexes as $annexe) {
                $this->Annex->id=$annexe['Annex']['id'];
                if($annexe['Annex']['joindre_fusion']){
                    $newAnnexe['edition_data'] = $this->Conversion->toOdt($annexe['Annex']['data'], $annexe['Annex']['filetype'], 'application/vnd.oasis.opendocument.text');
                    $newAnnexe['edition_data_typemime'] = 'application/vnd.oasis.opendocument.text';
                }
                if($annexe['Annex']['joindre_ctrl_legalite']){
                    $newAnnexe['data_pdf'] = $this->Conversion->convertirFlux($annexe['Annex']['data'], $DOC_TYPE[$annexe['Annex']['filetype']]['extension'], 'pdf');  
                }
                
                $this->Annex->save($newAnnexe);
            }
            
            if (empty($annexes))
                return Cron::MESSAGE_FIN_EXEC_SUCCES . " Aucune annexe à convertir";
            else
                return Cron::MESSAGE_FIN_EXEC_SUCCES . " Annexe convertie(s):\n\n" . count($annexes);
        } catch (Exception $e) {
            return Cron::MESSAGE_FIN_EXEC_ERROR .' '. $e->getMessage().' id='.(isset($this->Annex->id)?$this->Annex->id:'Inconnu');
        }
    }

    /**
     * Met à jour les ar et bordereau des dossiers envoyés en tdt
     */
    public function majArTdt(){
        App::uses('Deliberation', 'Model');
        $this->Deliberation = new Deliberation();
        try{
            $rapport = $this->Deliberation->majArAll();
            return Cron::MESSAGE_FIN_EXEC_SUCCES.$rapport;
        }catch (Exception $e){
            return Cron::MESSAGE_FIN_EXEC_ERROR . $e->getMessage();
        }
    }

    /**
     * Met à jour les echanges (TdtMessages) des dossiers envoyés en tdt
     */
    public function majCourriersTdt(){
        App::uses('Deliberation', 'Model');
        $this->Deliberation = new Deliberation();
        try{
            $rapport = $this->Deliberation->majEchangesTdtAll();
            return Cron::MESSAGE_FIN_EXEC_SUCCES . $rapport;
        }catch (Exception $e){
            return Cron::MESSAGE_FIN_EXEC_ERROR . $e->getMessage();
        }
    }
}