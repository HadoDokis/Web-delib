<?php

App::uses('AppTools', 'Lib');

class Cron extends AppModel
{
    public $belongsTo = array(
        'CreatedUser' => array(
            'className' => 'User',
            'foreignKey' => 'created_user_id'),
        'ModifiedUser' => array(
            'className' => 'User',
            'foreignKey' => 'modified_user_id')
    );

    // constantes de la classe
    // status d'exécution
    const EXECUTION_STATUS_SUCCES = 'SUCCES';
    const EXECUTION_STATUS_WARNING = 'WARNING';
    const EXECUTION_STATUS_FAILED = 'FAILED';
    const EXECUTION_STATUS_LOCKED = 'LOCKED';
    // status sous forme de chaines de caractères a insérer dans le rapport d'exécution des procédures appelées par les crons
    const MESSAGE_FIN_EXEC_SUCCES = 'TRAITEMENT_TERMINE_OK';
    const MESSAGE_FIN_EXEC_WARNING = 'TRAITEMENT_TERMINE_ALERTE';
    const MESSAGE_FIN_EXEC_ERROR = 'TRAITEMENT_TERMINE_ERREUR';

    // format de date
    const FORMAT_DATE = 'Y-m-d H:i:s';

    function beforeSave()
    {
        if (isset($this->data[$this->alias]['execution_duration']) && is_array($this->data[$this->alias]['execution_duration'])) {
            $this->data[$this->alias]['execution_duration'] = AppTools::arrayToDuration($this->data[$this->alias]['execution_duration']);
        }
        return true;
    }

    /**
     * retourne le libéllé du statut d'exécution
     */
    function libelleStatus($status)
    {
        switch ($status) {
            case self::EXECUTION_STATUS_LOCKED:
                $libelle = '<span class="label label-important" title="La tâche est vérrouillée, ce qui signifie qu\'elle est en cours d\'exécution ou dans un état bloqué suite à une erreur"><i class="fa fa-lock"></i> ' . __('Vérrouillée', true) . '</span>';
                break;
            case self::EXECUTION_STATUS_SUCCES:
                $libelle = '<span class="label label-success" title="Opération exécutée avec succès"><i class="fa fa-check"></i> ' . __('Exécutée avec succès', true) . '</span>';
                break;
            case self::EXECUTION_STATUS_WARNING:
                $libelle = '<span class="label label-warning" title="Avertissement(s) détecté(s) lors de l\'exécution, voir les détails de la tâche"><i class="fa fa-info"></i> ' . __('Exécutée, en alerte', true) . '</span>';
                break;
            case self::EXECUTION_STATUS_FAILED:
                $libelle = '<span class="label label-important" title="Erreur(s) détectée(s) lors de l\'exécution, voir les détails de la tâche"><i class="fa fa-warning"></i> ' . __('Non exécutée : erreur', true) . '</span>';
                break;
            default:
                $libelle = '<span class="label" title="Statut indéfini, la tâche a t-elle déjà été exécutée ?"><i class="fa fa-question"></i> ' . __('Indéfini', true) . '</span>';
        }
        return $libelle;
    }

    /**
     * Retourne le libellé correspondant à l'état 'active'
     */
    function libelleActive($active)
    {
        return $active ? __('Oui', true) : __('Non', true);
    }

    /**
     * Retourne la forme litérale de 'execution_duration'
     */
    function libelleDuration($duration)
    {
        return AppTools::durationToString($duration);
    }

    /**
     * retourne la prochaine date d'exécution pour le cron $cron
     * @param integer|array $idOuData id ou tableau de données de l'enregistrement
     * @return string date-heure de la prochaine exécution sous forme 'Y-m-d H:i:s'
     */
    function calcNextExecutionTime($idOuData)
    {
        // initialisations
        $now = date(self::FORMAT_DATE);
        // lecture en base ou initialisation de l'archive
        if (is_array($idOuData))
            $data = $idOuData;
        else
            $data = $this->find('first', array(
                'recursive' => -1,
                'conditions' => array('id' => $idOuData)));

        if (empty($data['Cron']['next_execution_time']))
            $nextExecutionTime = $now;
        else
            $nextExecutionTime = $data['Cron']['next_execution_time'];

        $nowplusdelais = AppTools::addSubDurationToDate($now, $data['Cron']['execution_duration'], self::FORMAT_DATE);

        if ($nextExecutionTime < $nowplusdelais) {
            $nextExecutionTime = $nowplusdelais;
        }
        return $nextExecutionTime;
    }

}
