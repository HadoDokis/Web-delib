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

    var $name = 'TdtMessage';
    var $useTable = "tdt_messages";
    var $belongsTo = array('Deliberation' =>
        array('className' => 'Deliberation',
            'foreignKey' => 'delib_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'counterCache' => ''
        )
    );

    public function isNewMessage($delib_id, $type, $reponse, $message_id) {
        $message = $this->find('first', array('conditions' =>
            array('TdtMessage.delib_id' => $delib_id,
                'TdtMessage.type_message' => $type,
                'TdtMessage.reponse' => $reponse,
                'TdtMessage.message_id' => $message_id)));
        return (empty($message));
    }

}
