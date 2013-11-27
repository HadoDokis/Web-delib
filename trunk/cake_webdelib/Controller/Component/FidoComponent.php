<?php
/**
 * Application: webdelib / Adullact.
 * Date: 26/11/13
 * @author: Florian Ajir <florian.ajir@adullact.org>
 * @license CeCiLL V2 <http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html>
 */

class FidoComponent extends Component{

    public $formats;

    public $lastResults;

    function FidoComponent() {
        require_once(APP.DS.'Lib'.DS.'fido.php');
        $this->formats = Configure::read("DOC_TYPE");
        $this->lastResults = array();
    }

    public function analyzeFile($file){
        $this->lastResults = Fido::analyzeFile($file);
        if ($this->lastResults['result'] == 'OK'){
            $this->_getDetails();
            return $this->lastResults;
        }else{
            return false;
        }
    }

    private function _checkFormat($mime, $puid){
        if (isset($this->formats[$mime]['puid'][$puid])){
            return $this->formats[$mime]['puid'][$puid]['actif'];
        }else{
            return false;
        }
    }

    public function checkFile($file){
        $this->lastResults = Fido::analyzeFile($file);
        if ($this->lastResults['result'] == 'OK'){
            $this->_getDetails();
            return $this->_checkFormat($this->lastResults['mimetype'], $this->lastResults['puid']);
        }else{
            return false;
        }
    }

    private function _getDetails(){
        //Extrait les infos de la config
        $configDetails = $this->formats[$this->lastResults['mimetype']];

        //Retire la branche avec les puid
        $configDetails = Hash::remove($configDetails, 'puid');
        if (isset ($this->formats[$this->lastResults['mimetype']]['puid'][$this->lastResults['puid']]))
            $this->lastResults['actif'] = $this->formats[$this->lastResults['mimetype']]['puid'][$this->lastResults['puid']]['actif'];
        else{
            $this->log($this->lastResults, 'error');
            $this->lastResults['actif'] = false;
        }
        $this->lastResults = Hash::merge($this->lastResults, $configDetails);

        return $this->lastResults;
    }

}