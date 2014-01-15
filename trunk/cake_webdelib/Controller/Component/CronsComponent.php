<?php
/**
 * Application: webdelib / Adullact.
 * Date: 13/01/14
 * @author: Florian Ajir <florian.ajir@adullact.org>
 * @license CeCiLL V2 <http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html>
 */
App::uses('Cron', 'Model');
App::uses('CronJob', 'Model');
class CronsComponent extends Component
{
    public function startup($controller)
    {
        //Chargement du Model "Cron"
        $this->Cron = ClassRegistry::init('Cron');
        parent::startup($controller);
    }

    /**
     * fonction d'exécution d'un cron par son id (actif ou non)
     * @param integer $id id du cron a exécuter
     * @return bool|string
     */
    public function runCronId($id)
    {

        try {
            // initialisation date
            $lastExecutionStartTime = date(Cron::FORMAT_DATE);

            // lecture du cron à exécuter
            $cron = $this->Cron->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $id,
                    'active' => true,
                    'lock' => false
                )));

            // Sortie si tâche non trouvée
            if (empty($cron))
                return;

            $this->Cron->id = $id;

            //Verrouille la tâche pour éviter les exécutions parallèles
//            $this->Cron->saveField('lock', true);

            // Chargement de la classe
            $caller = ucfirst($cron['Cron']['model']);
            if (!empty($cron['Cron']['plugin']))
                $caller = ucfirst($cron['Cron']['plugin']).'.'.$caller;

            $this->{$cron['Cron']['model']} = ClassRegistry::init($caller);

            // Execution
            if (!empty($cron['Cron']['has_params']))
                $output = $this->{$cron['Cron']['model']}->{$cron['Cron']['action']}(explode(',',$cron['Cron']['params']));
            else
                $output = $this->{$cron['Cron']['model']}->{$cron['Cron']['action']}();

        } catch (Exception $e) {
            $output = Cron::MESSAGE_FIN_EXEC_ERROR. "Exception levée : \n". $e->getMessage();
            $this->log($e->getTraceAsString(), 'error');
        }

        // initialisation du rapport d'exécution
        $rappExecution = str_replace(array(Cron::MESSAGE_FIN_EXEC_SUCCES, Cron::MESSAGE_FIN_EXEC_WARNING, Cron::MESSAGE_FIN_EXEC_ERROR), '', $output);

        // mise à jour du cron
        $cron['Cron']['lock'] = false;
        $cron['Cron']['last_execution_start_time'] = $lastExecutionStartTime;
        if (strpos($output, Cron::MESSAGE_FIN_EXEC_SUCCES) !== false) {
            $cron['Cron']['last_execution_status'] = Cron::EXECUTION_STATUS_SUCCES;
            $cron['Cron']['next_execution_time'] = $this->Cron->calcNextExecutionTime($cron);
        } elseif (strpos($output, Cron::MESSAGE_FIN_EXEC_WARNING) !== false) {
            $cron['Cron']['last_execution_status'] = Cron::EXECUTION_STATUS_WARNING;
            $cron['Cron']['next_execution_time'] = $this->Cron->calcNextExecutionTime($cron);
        } else {
            $cron['Cron']['last_execution_status'] = Cron::EXECUTION_STATUS_FAILED;
        }
        $cron['Cron']['last_execution_end_time'] = date(Cron::FORMAT_DATE);
        $cron['Cron']['last_execution_report'] = $rappExecution;
        if ($this->Cron->save($cron)){
            return $output;
        }else{
            return Cron::EXECUTION_STATUS_FAILED.$output;
        }
    }

    /**
     * Exécute toutes les tâches planifiée en attente
     * Vérifie si la date/time d'execution prévue est inférieur à la date/time du jour
     * OU si le délais entre 2 éxecutions est dépassé
     */
    public function runPending(){
        $rapport = date(Cron::FORMAT_DATE)."\n";
        // lecture des crons à exécuter
        $crons = $this->Cron->find('all', array(
            'recursive' => -1,
            'fields' => array('id','nom'),
            'conditions' => array(
                'next_execution_time <= ' => date(Cron::FORMAT_DATE),
                'active' => true,
                'lock' => false
            ),
            'order' => array('next_execution_time ASC')));
        if (!empty($crons)){
            // exécutions
            foreach ($crons as $cron) {
                $rapport .= $cron['Cron']['id'].'-'.$cron['Cron']['nom'].' : '.$this->runCronId($cron['Cron']['id'])."\n";
            }
        }else{
            $rapport .= "Aucune tâche planifiée à exécuter";
        }
        return $rapport;
    }

    public function runAll(){
        // lecture des crons à exécuter
        $crons = $this->Cron->find('all', array(
            'recursive' => -1,
            'fields' => array('id'),
            'conditions' => array(
                'active' => true,
                'lock' => false
            ),
            'order' => array('next_execution_time ASC')));

        // exécutions
        if (!empty($crons))
            foreach ($crons as $cron)
                $this->runCronId($cron['Cron']['id']);
    }
}