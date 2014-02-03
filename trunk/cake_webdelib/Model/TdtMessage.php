<?php

/**
 * Code source de la classe TdtMessage.
 *
 * PHP 5.3
 *
 * @package app.Model.TdtMessage
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * Classe TdtMessage.
 *
 * @package app.Model.TdtMessage
 *
 */
class TdtMessage extends AppModel {

    public $name = 'TdtMessage';
    public $useTable = "tdt_messages";
    public $belongsTo = array(
        'Deliberation' => array(
            'className' => 'Deliberation',
            'foreignKey' => 'delib_id',
            'conditions' => '',
            'fields' => '',
            'order' => 'date_message',
            'counterCache' => ''
        )
    );

    public function existe($message_id) {
        $message = $this->find('count', array(
            'conditions' => array(
                'TdtMessage.message_id' => $message_id
            )));
        return !empty($message);
    }

}
