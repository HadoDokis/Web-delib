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

    public $useTable = "tdt_messages";
    public $belongsTo = array(
        'Deliberation' => array(
            'foreignKey' => 'delib_id',
        )
    );
    public $hasMany = array(
        'Reponse' => array(
            'className' => 'TdtMessage',
            'foreignKey' => 'parent_id',
            'order' => 'tdt_id ASC',
            'dependent' => true),
    );
}
