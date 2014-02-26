<?php
/**
* Code source de la classe Histochoixcer93Test.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/
App::uses('CronJob', 'Model');

/**
* Classe Histochoixcer93Test.
*
* @package app.Test.Case.Model
* 
*/

class CronJobTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
                        'app.annex',
                        'app.deliberation'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
            parent::setUp();
            $this->CronJob = ClassRegistry::init('CronJob');
            $this->Annex = ClassRegistry::init('Annex');
                
            $this->Annex->id=215;
            $aData['data']=file_get_contents(APP.'Test/Data/AnnexFixture.pdf');
            $this->Annex->save($aData, false);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
                unset($this->CronJob);
		unset($this->Annex);

		parent::tearDown();
	}

/**
 * testIsNewMessage method
 *
 * @return void
 */
	public function testconvertionAnnexesJob() {
             
            $this->CronJob->convertionAnnexesJob(341);
             
            $this->Annex = new Annex();
            $annexe = $this->Annex->find('first', array(
                'fields' => array('id','data','filename','filetype'),
                'conditions' => array('edition_data IS NOT NULL','data_pdf IS NOT NULL','foreign_key'=>341),
                'recursive'=>-1
            ));
            
            debug($annexe);
            
            $this->assertEquals(1, count($annexe), var_export( count($annexe), true));
            
	}

}
