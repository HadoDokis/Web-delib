<?php

/**
 * Code source de la classe CommentaireFixture.
 *
 * PHP 5.3
 *
 * @package app.Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe CommentaireFixture ...
 *
 * @package app.Test.Fixture
 */
class CommentaireFixture extends CakeTestFixture {

    /**
     * On importe la définition de la table, pas les enregistrements.
     *
     * @var array
     */
    public $import = array(
        'model' => 'Commentaire',
        'records' => false
    );

    /**
     * Définition des enregistrements.
     *
     * @var array
     */
    public $records = array(
        array(
            'id' => 9,
            'delib_id' => 1,
            'agent_id' => 8,
            'texte' => 'A revoir (cf notre conversation du 5/06)',
            'pris_en_compte' => 1,
            'commentaire_auto' => false,
            'created' => '2013-06-06 10:44:21',
            'modified' => '2013-06-06 10:44:21'
        ),
        array(
            'id' => 10,
            'delib_id' => 1,
            'agent_id' => 10,
            'texte' => 'OK, pris en compte',
            'pris_en_compte' => 0,
            'commentaire_auto' => false,
            'created' => '2013-06-06 10:47:05',
            'modified' => '2013-06-06 10:47:05'
        ),
        array(
            'id' => 32,
            'delib_id' => 2,
            'agent_id' => 0,
            'texte' => 'A reçu un avis défavorable en Commission 1 du Mercredi 26 juin 2013',
            'pris_en_compte' => 0,
            'commentaire_auto' => true,
            'created' => '2013-06-12 11:02:17',
            'modified' => '2013-06-12 11:02:17'
        )
    );

}

?>