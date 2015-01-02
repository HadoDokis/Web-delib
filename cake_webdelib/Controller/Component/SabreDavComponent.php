<?php
/**
* Code source de la classe Histochoixcer93Test.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/
//App::uses('Sabre/DAV', 'Vendor');

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

//App::uses('CakeSabreDav.Sabre/DAV', 'Vendor');
/**
* Classe SabreDavComponent.
*
* @package app.Controller.Component
* 
*/
use Sabre\DAV;

class SabreDavComponent extends Component {
    
    public $components = array('Session');
    
    private $_folder ;
    
    function SabreDavComponent() {
    }
    
    public function initialize($controller) {
    
        //$this->Session->read('user.User.id')
        $sDir=$controller->request->controller . (!empty($controller->request->action)? DS . $controller->request->action:'');
        $this->_folder  = new Folder(AppTools::newTmpDir(TMP . 'files' . DS . 'webdav' . DS . date('Ymd')  . DS . CakeSession::read('user.User.id') . DS .$sDir), true, 0777);
        
    }
    
    public function Server() {
        
        $publicDir = new MyDirectory(TMP .'files'. DS. 'webdav');
        
        $server = new DAV\Server($publicDir);
        
        $server->setBaseUri('/webdav');
        
        /*$lockBackend = new DAV\Locks\Backend\File(TMP .'files'. DS .'webdav'. DS .'locks.dat');
        $lockPlugin = new DAV\Locks\Plugin($lockBackend);
        $server->addPlugin($lockPlugin);*/
        
        //$server->addPlugin(new DAV\Browser\Plugin());
        
        $server->exec();
        exit;
    }
    
    public function newFileDav($filename, $data)
    {
        
        $file = new File($this->_folder->pwd() . DS . $filename, true, 0777);
        $file->append($data);
        
        return Configure::read('PROTOCOLE_DL') . "://" . $_SERVER['SERVER_NAME']. DS . strstr($file->pwd(), 'webdav/');
    }
}

class MyDirectory extends DAV\Collection {

  private $myPath;

  function __construct($myPath) {
        $this->myPath = $myPath;
  }

  function getChildren() {

    $children = array();
    // Loop through the directory, and create objects for each node
    foreach(scandir($this->myPath) as $node) {
      // Ignoring files staring with .
      if ($node[0]==='.') continue;
      $children[] = $this->getChild($node);

    }

    return $children;

  }

  function getChild($name) {

      $path = $this->myPath . '/' . $name;

      // We have to throw a NotFound exception if the file didn't exist
      if (!file_exists($path)) {
        throw new DAV\Exception\NotFound('The file with name: ' . $path . ' could not be found');
      }

      // Some added security
      if ($name[0]=='.')  throw new DAV\Exception\NotFound('Access denied');

      if (is_dir($path)) {

          return new MyDirectory($path);

      } else {

          return new MyFile($path);

      }

  }

  function childExists($name) {

        return file_exists($this->myPath . '/' . $name);

  }

  function getName() {

      return basename($this->myPath);

  }
  
  function getLastModified() {

      return '';

  }
  
  public function createFile($name, $data = null) {
      
    $file = new File( $this->myPath. DS . $name, true, 0777);
    $file->append(stream_get_contents($data));
    $file->close();

    //throw new DAV\Exception\Forbidden('à implémenter');

  }
  
  public function createDirectory($name) {

    $folder= new Folder( $this->myPath.DS.$name, true);
        
    //throw new DAV\Exception\Forbidden($this->myPath.$name);

}

}

class MyFile extends DAV\File {

  private $myPath;

  function __construct($myPath) {
      
    $this->myPath = $myPath;

  }

  function getName() {

    return basename($this->myPath);

  }

  function get() {

    return fopen($this->myPath,'rb');

  }
  
  function put($data) {
      
    $file = new File( $this->myPath, true, 0777);
    $file->append(stream_get_contents($data));
    $file->close();
  }

  function getSize() {

    return filesize($this->myPath);

  }

  function getETag() {

    return '"' . md5_file($this->myPath) . '"';

  }
  
  	/**
    * Returns the mime-type for a file
    *
    * If null is returned, we'll assume application/octet-stream
    *
    * @return mixed
    */
    public function getContentType() {
        
        return 'application/vnd.oasis.opendocument.text';
    
    }
}