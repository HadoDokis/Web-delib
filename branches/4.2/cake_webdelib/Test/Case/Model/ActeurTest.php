<?php

/**
 * Code source de la classe ActeurTest.
 *
 * @package app.Test.Case.Model
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses('Acteur', 'Model');

/**
 * La classe ActeurTest ...
 *
 * @package app.Test.Case.Model
 */
class ActeurTest extends CakeTestCase {

    /**
     *
     * @var Model
     */
    public $Acteur = null;

    /**
     * Fixtures utilisés.
     *
     * @var array
     */
    public $fixtures = array(
        'app.Acteur'
    );

    /**
     * Préparation du test.
     */
    public function setUp() {
        parent::setUp();
        $this->Acteur = ClassRegistry::init('Acteur');
    }

    /**
     * Nettoyage postérieur au test.
     */
    public function tearDown() {
        unset($this->Acteur);
        parent::tearDown();
    }

    /**
     * Test de la méthode Acteur::gedoooNormalize()
     */
    public function testGedoooNormalize() {
        /**
         * Premier test
         */
        $record1 = array(
            'President' => array(
                'id' => 1,
                'typeacteur_id' => 1,
                'nom' => 'PAILLAT',
                'prenom' => 'Jean',
                'salutation' => 'M.',
                'titre' => 'Maire',
                'position' => 1,
                'date_naissance' => '1989-11-10',
                'adresse1' => '2 rue de la Poste',
                'adresse2' => 'chez Bernard',
                'cp' => '33600',
                'ville' => 'PESSAC',
                'email' => 'florian.ajir@adullact-projet.coop',
                'telfixe' => '0123456789',
                'telmobile' => '0612345678',
                'suppleant_id' => 13,
                'note' => 'cet homme est un élu',
                'actif' => true,
                'created' => '2013-06-05 11:34:01',
                'modified' => '2013-06-13 17:20:10'
            )
        );
        $result1 = $this->Acteur->gedoooNormalize('acteur_mandate', false, 'President', $record1);
        $expected1 = array(
            'nom_acteur_mandate' => 'PAILLAT',
            'prenom_acteur_mandate' => 'Jean',
            'salutation_acteur_mandate' => 'M.',
            'titre_acteur_mandate' => 'Maire',
            'position_acteur_mandate' => 1,
            'date_naissance_acteur_mandate' => '1989-11-10',
            'adresse1_acteur_mandate' => '2 rue de la Poste',
            'adresse2_acteur_mandate' => 'chez Bernard',
            'cp_acteur_mandate' => '33600',
            'ville_acteur_mandate' => 'PESSAC',
            'email_acteur_mandate' => 'florian.ajir@adullact-projet.coop',
            'telfixe_acteur_mandate' => '0123456789',
            'telmobile_acteur_mandate' => '0612345678',
            'note_acteur_mandate' => 'cet homme est un élu',
        );
        $this->assertEquals($result1, $expected1, var_export($result1, true));

        /**
         * Deuxième test
         */
        $record2 = array(
            'Secretaire' => array(
                'id' => 4,
                'typeacteur_id' => 1,
                'nom' => 'VILLON',
                'prenom' => 'Amandine',
                'salutation' => 'Mme',
                'titre' => 'Adjointe',
                'position' => 6,
                'date_naissance' => '1979-11-15',
                'adresse1' => '5 rue de la piscine',
                'adresse2' => 'test',
                'cp' => '33600',
                'ville' => 'PESSAC',
                'email' => 'test@unitaire.org',
                'telfixe' => '0123456789',
                'telmobile' => '0612345678',
                'suppleant_id' => null,
                'note' => 'est l\'adjointe au maire',
                'actif' => true,
                'created' => '2013-06-05 11:36:08',
                'modified' => '2013-06-05 11:36:08'
            )
        );
        $expected2 = array(
            'nombre_acteur_mandataire' => 20,
            "nom_acteur_mandataire" => 'VILLON',
            "prenom_acteur_mandataire" => 'Amandine',
            "salutation_acteur_mandataire" => 'Mme',
            "titre_acteur_mandataire" => 'Adjointe',
            'position_acteur_mandataire' => 6,
            "date_naissance_acteur_mandataire" => '1979-11-15',
            "adresse1_acteur_mandataire" => '5 rue de la piscine',
            "adresse2_acteur_mandataire" => 'test',
            "cp_acteur_mandataire" => '33600',
            "ville_acteur_mandataire" => 'PESSAC',
            "email_acteur_mandataire" => 'test@unitaire.org',
            "telfixe_acteur_mandataire" => '0123456789',
            "telmobile_acteur_mandataire" => '0612345678',
            "note_acteur_mandataire" => 'est l\'adjointe au maire',
        );
        $result2 = $this->Acteur->gedoooNormalize('acteur_mandataire', 20, 'Secretaire', $record2);
        $this->assertEquals($result2, $expected2, var_export($result2, true));
    }

    /**
     * Test de la méthode Acteur::gedoooNormalizeList()
     * FIXME tester avec mandataire
     */
    public function testgedoooNormalizeList() {
        $records = array(
            array(
                'Listepresence' => array(
                        'id' => 1814,
                        'delib_id' => 276,
                        'acteur_id' => 8,
                        'present' => true,
                        'mandataire' => null,
                        'suppleant_id' => null
                ),
                'Acteur' => array(
                        'id' => 8,
                        'typeacteur_id' => 2,
                        'nom' => 'HENRY',
                        'prenom' => 'Jean-Pierre',
                        'salutation' => 'M.',
                        'titre' => 'Conseiller Municipal',
                        'position' => 39,
                        'date_naissance' => null,
                        'adresse1' => '',
                        'adresse2' => '',
                        'cp' => '',
                        'ville' => '',
                        'email' => '',
                        'telfixe' => '',
                        'telmobile' => '',
                        'suppleant_id' => null,
                        'note' => '',
                        'actif' => true,
                        'created' => '2013-02-14 16:20:20',
                        'modified' => '2013-02-14 16:20:20'
                ),
                'VoteActeur' => array(
                        'id' => 828,
                        'acteur_id' => 8,
                        'delib_id' => 276,
                        'resultat' => 2,
                        'created' => '2013-08-19 17:01:43',
                        'modified' => '2013-08-19 17:01:43'
                ),
                'Mandataire' => array(
                        'id' => null,
                        'typeacteur_id' => null,
                        'nom' => null,
                        'prenom' => null,
                        'salutation' => null,
                        'titre' => null,
                        'position' => null,
                        'date_naissance' => null,
                        'adresse1' => null,
                        'adresse2' => null,
                        'cp' => null,
                        'ville' => null,
                        'email' => null,
                        'telfixe' => null,
                        'telmobile' => null,
                        'suppleant_id' => null,
                        'note' => null,
                        'actif' => null,
                        'created' => null,
                        'modified' => null
                ),
                'VoteMandataire' => array(
                        'id' => null,
                        'acteur_id' => null,
                        'delib_id' => null,
                        'resultat' => null,
                        'created' => null,
                        'modified' => null
                ),
                'Suppleant' => array(
                        'id' => null,
                        'typeacteur_id' => null,
                        'nom' => null,
                        'prenom' => null,
                        'salutation' => null,
                        'titre' => null,
                        'position' => null,
                        'date_naissance' => null,
                        'adresse1' => null,
                        'adresse2' => null,
                        'cp' => null,
                        'ville' => null,
                        'email' => null,
                        'telfixe' => null,
                        'telmobile' => null,
                        'suppleant_id' => null,
                        'note' => null,
                        'actif' => null,
                        'created' => null,
                        'modified' => null
                ),
                'VoteSuppleant' => array(
                        'id' => null,
                        'acteur_id' => null,
                        'delib_id' => null,
                        'resultat' => null,
                        'created' => null,
                        'modified' => null
                )
        ),
        array(
                'Listepresence' => array(
                        'id' => 1813,
                        'delib_id' => 276,
                        'acteur_id' => 7,
                        'present' => true,
                        'mandataire' => null,
                        'suppleant_id' => null
                ),
                'Acteur' => array(
                        'id' => 7,
                        'typeacteur_id' => 2,
                        'nom' => 'EPANYA',
                        'prenom' => 'Augusta',
                        'salutation' => 'Mme',
                        'titre' => 'Conseillère Municipale',
                        'position' => 40,
                        'date_naissance' => null,
                        'adresse1' => '',
                        'adresse2' => '',
                        'cp' => '',
                        'ville' => '',
                        'email' => '',
                        'telfixe' => '',
                        'telmobile' => '',
                        'suppleant_id' => null,
                        'note' => '',
                        'actif' => true,
                        'created' => '2013-02-14 16:19:33',
                        'modified' => '2013-02-14 16:19:33'
                ),
                'VoteActeur' => array(
                        'id' => 829,
                        'acteur_id' => 7,
                        'delib_id' => 276,
                        'resultat' => 2,
                        'created' => '2013-08-19 17:01:43',
                        'modified' => '2013-08-19 17:01:43'
                ),
                'Mandataire' => array(
                        'id' => null,
                        'typeacteur_id' => null,
                        'nom' => null,
                        'prenom' => null,
                        'salutation' => null,
                        'titre' => null,
                        'position' => null,
                        'date_naissance' => null,
                        'adresse1' => null,
                        'adresse2' => null,
                        'cp' => null,
                        'ville' => null,
                        'email' => null,
                        'telfixe' => null,
                        'telmobile' => null,
                        'suppleant_id' => null,
                        'note' => null,
                        'actif' => null,
                        'created' => null,
                        'modified' => null
                ),
                'VoteMandataire' => array(
                        'id' => null,
                        'acteur_id' => null,
                        'delib_id' => null,
                        'resultat' => null,
                        'created' => null,
                        'modified' => null
                ),
                'Suppleant' => array(
                        'id' => null,
                        'typeacteur_id' => null,
                        'nom' => null,
                        'prenom' => null,
                        'salutation' => null,
                        'titre' => null,
                        'position' => null,
                        'date_naissance' => null,
                        'adresse1' => null,
                        'adresse2' => null,
                        'cp' => null,
                        'ville' => null,
                        'email' => null,
                        'telfixe' => null,
                        'telmobile' => null,
                        'suppleant_id' => null,
                        'note' => null,
                        'actif' => null,
                        'created' => null,
                        'modified' => null
                ),
                'VoteSuppleant' => array(
                        'id' => null,
                        'acteur_id' => null,
                        'delib_id' => null,
                        'resultat' => null,
                        'created' => null,
                        'modified' => null
                )
            )
        );
        $results1 = $this->Acteur->gedoooNormalizeAll('present', $records);
        //$results1 = $this->Acteur->gedoooNormalizeAll('mandate', $records);
        debug($results1);
//        $this->assertEquals($results1, $expected1, var_export($results1, true));

        $results2 = $this->Acteur->gedoooNormalizeAll('mandate', $records);
        debug($results2);
//        $this->assertEquals($results2, $expected2, var_export($results2, true));
//        $this->assertEquals($result, $expected, var_export($result, true));
        $this->markTestIncomplete('Ce test n\'a pas encoré été finalisé (FIXME model Acteur : ActeurMandate).');
    }

}

?>
