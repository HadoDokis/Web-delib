<?php
return;
App::uses('SeleniumCakeTestCase', 'Lib');

class ConnexionSelenium extends SeleniumCakeTestCase {

    /**
     * autoFixtures property
     *
     * @var bool false
     */
    public $autoFixtures = true;

    /**
     * Whether backup global state for each test method or not
     *
     * @var bool false
     */
    public $backupGlobals = false;

    /**
     * fixtures property
     *
     * @var array
     */
    public $fixtures = array(
        'app.user',
        //'app.aco',
        'app.aro',
        //'app.aros_aco'
        'app.Session'
    );
    protected $captureScreenshotOnFailure = FALSE;
    protected $screenshotPath = '/var/www/splaza/branches/v4.1.04/app/webroot/screenshots';
    protected $screenshotUrl = 'http://webdelib-v4.1.04.dev.adullact.org/screenshots';
    private $test_user = 'admin';
    private $test_password = 'admin';
    public static $browsers = array(
        array(
            'browserName' => 'firefox',
            'sessionStrategy' => 'shared',
        )
    );

    public function setUp() {
        parent::setUp();
        $this->setBrowser('firefox');
        $this->setHost('127.0.0.1');
        $this->setPort(4444);
        $this->setBrowserUrl(Configure::Read('WEBDELIB_URL'));
        $this->shareSession(true);

        //$this->loadFixtures('Session');
    }

    /*
     * Given i prepared the app with valid test_user
     * When i log in the appplication with a correct password
     * Then i should be on "Inscription sur liste d'attente" page
     *
     */

    public function testConnection() {
        $this->url('/users/login');
        $this->timeouts()->implicitWait(10000);
        $userloginInput = $this->byId('UserLogin');
        $userloginInput->value($this->test_user);
        $userpasswordInput = $this->byId('UserPassword');
        $userpasswordInput->value($this->test_password);
        $submit = $this->byCssSelector('button.btn');
        $submit->click();
        $this->timeouts()->implicitWait(10000);
        $this->assertEquals("Webdelib: Home", $this->title());
    }

    /**
     * Method testAjoutTestDroitsTestAdmin 
     * @test 
     */
    public function testAjoutTestDroitsTestAdmin() {
        $this->url("/users/index");
        $this->byCssSelector("table tr:nth-child(2) td.actions a.link_modifier")->click();
        $this->byId("lienTab2")->click();
        // click //table[@id='tableDroits']/tbody/tr/td/div/input[@type='checkbox'] //ne fonctionne pas
        // click css=table#tableDroits input[type='checkbox'] //Ne fonctionne pas
        $this->byId("chkBoxDroits0")->click();
        $this->byId("chkBoxDroits1")->click();
        $this->byId("chkBoxDroits8")->click();
        $this->byId("chkBoxDroits10")->click();
        $this->byId("chkBoxDroits15")->click();
        $this->byId("chkBoxDroits20")->click();
        $this->byId("chkBoxDroits25")->click();
        $this->byId("chkBoxDroits31")->click();
        $this->byId("chkBoxDroits36")->click();
        $this->byId("chkBoxDroits39")->click();
        $this->byId("chkBoxDroits51")->click();
        $this->byId("chkBoxDroits55")->click();
        $this->byId("boutonValider")->click();
        // Warning: assertTextPresent may require manual changes
        $this->assertTrue((bool) preg_match('/^[\s\S]*Liste des utilisateurs[\s\S]*$/', $this->byCssSelector("BODY")->text()));
        $result = $this->byCssSelector("table tr")->size();
        $this->assertEquals(2, $result);
    }

    public function testDeconnection() {
        $this->url('/users/logout');
        $this->timeouts()->implicitWait(10000);
        $this->assertEquals("Webdelib: Users", $this->title());
    }

    /** generate screenshot if any test has failed */
    /* protected function tearDown()
      {
      if( $this->hasFailed() ) {
      $date = "screenshot_" . date('Y-m-d-H-i-s') . ".png" ;
      $this->webdriver->getScreenshotAndSaveToFile( $date );
      }
      $this->close();
      } */
}
