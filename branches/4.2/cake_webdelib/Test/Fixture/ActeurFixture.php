<?php

/**
 * Code source de la classe ActeurFixture.
 *
 * @package app.Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe ActeurFixture ...
 *
 * @package app.Test.Fixture
 */
class ActeurFixture extends CakeTestFixture {

    /**
     * On importe la définition de la table, pas les enregistrements.
     *
     * @var array
     */
    public $import = array(
        'model' => 'Acteur',
        'records' => false
    );

    /**
     * Définition des enregistrements.
     *
     * @var array
     */
    public $records = array(
        array(
            'id' => 1,
            'typeacteur_id' => 1,
            'nom' => 'PAILLAT',
            'prenom' => 'Jean',
            'salutation' => 'M.',
            'titre' => 'Maire',
            'position' => 1,
            'date_naissance' => null,
            'adresse1' => '2 rue de la Poste',
            'adresse2' => '',
            'cp' => '33600',
            'ville' => 'PESSAC',
            'email' => 'florian.ajir@adullact-projet.coop',
            'telfixe' => '',
            'telmobile' => '',
            'suppleant_id' => 2,
            'note' => '',
            'actif' => true,
            'created' => '2013-06-05 11:34:01',
            'modified' => '2013-06-13 17:20:10'
        ), 
        array(
            'id' => 2,
            'typeacteur_id' => 1,
            'nom' => 'VILLON',
            'prenom' => 'Amandine',
            'salutation' => 'Mme',
            'titre' => 'Adjointe',
            'position' => 6,
            'date_naissance' => null,
            'adresse1' => '5 rue de la piscine',
            'adresse2' => '',
            'cp' => '33600',
            'ville' => 'PESSAC',
            'email' => '',
            'telfixe' => '',
            'telmobile' => '',
            'suppleant_id' => null,
            'note' => '',
            'actif' => true,
            'created' => '2013-06-05 11:36:08',
            'modified' => '2013-06-05 11:36:08'
        ),
        array(
            'id' => 3,
            'typeacteur_id' => 2,
            'nom' => 'Ajir',
            'prenom' => 'Florian',
            'salutation' => 'M.',
            'titre' => 'Maitre',
            'position' => 3,
            'date_naissance' => '1989-11-10',
            'adresse1' => '836 rue du mas de verchant',
            'adresse2' => 'Adullact',
            'cp' => '34000',
            'ville' => 'Montpellier',
            'email' => 'florian.ajir@adullact-projet.coop',
            'telfixe' => '0123456789',
            'telmobile' => '0701234567',
            'suppleant_id' => null,
            'note' => 'donnée de test',
            'actif' => true,
            'created' => '2013-06-05 11:34:01',
            'modified' => '2013-06-13 17:20:10'
        )
    );
    
}

?>