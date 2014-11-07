      <?php
      return;
      App::uses('SeleniumCakeTestCase', 'Lib');
     
      class ConnexionSelenium extends SeleniumCakeTestCase  {
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

          
          public function setUp(){
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
          public function testConnectionEchec()
          {
                $this->url('/users/login');
                $this->timeouts()->implicitWait(10000);
                $userloginInput = $this->byId('UserLogin');
                $userloginInput->value($this->test_user);
                $userpasswordInput = $this->byId('UserPassword');
                $userpasswordInput->value('');
                $submit = $this->byCssSelector('button.btn');
                $submit->click();
                $this->timeouts()->implicitWait(10000);
                //$element = $this->byCssSelector('error-message');
                //$this->assertEquals("Mauvais identifiant ou  mot de passe.Veuillez recommencer.", $element->text());
                $this->assertEquals("Webdelib: Users", $this->title());
          }
          
          public function testConnection()
          {
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
          
          public function testDeconnection()
          {
                $this->url('/users/logout');
                $this->timeouts()->implicitWait(10000);
                $this->assertEquals("Webdelib: Users", $this->title());
          }
          
    /** generate screenshot if any test has failed */
    /*protected function tearDown()
    {
        if( $this->hasFailed() ) {
            $date = "screenshot_" . date('Y-m-d-H-i-s') . ".png" ;
            $this->webdriver->getScreenshotAndSaveToFile( $date );
        }
        $this->close();
    }*/

      }