<?php
class ModelsController extends AppController {

	public $uses = array(
        'Deliberation', 'User',  'Annex', 'Typeseance', 'Seance', 'Service', 'Commentaire',
        'Theme', 'Collectivite', 'Vote', 'Listepresence', 'Acteur', 'Infosupdef', 'Infosuplistedef', 'Historique', 'ModelOdtValidator.Modeltemplate'
    );
    public $helpers = array('Fck');
    public $components = array('RequestHandler','Email', 'Acl', 'Gedooo', 'Conversion', 'Progress');

	// Gestion des droits
    public $aucunDroit = array(
			'generer',
			'getGeneration',
			'genereToken',
			'paramMails'
	);

    public $commeDroit = array(
			'getFileData'  => 'Models:index',
			'changeStatus' => 'Models:index'
	);

	function _getFileName($id=null) {
		$objCourant = $this->Modeltemplate->find('first', array(
				'conditions'=> array('Modeltemplate.id'=> $id),
				'recursive' => '-1',
				'fields'    => 'filename'));
		return utf8_encode($objCourant['Modeltemplate']['filename']);
	}

	function _getSize($id=null) {
		$objCourant = $this->Modeltemplate->find('first', array(
				'conditions'=> array('Modeltemplate.id'=> $id),
				'recursive' => '-1',
				'fields'    => 'filesize'));
		return $objCourant['Modeltemplate']['filesize'];
	}

	function _getModel($id=null) {
		$objCourant = $this->Modeltemplate->find('first', array(
				'conditions'=> array('Modeltemplate.id'=> $id),
				'recursive' => '-1',
				'fields'    => 'content'));
		return $objCourant['Modeltemplate']['content'];
	}
        
        function genereToken(){
            if ($this->request->is('get')) {
                $this->autoRender = false;
            
                $this->response->type('json', 'text/x-json');
                $this->RequestHandler->respondAs('json'); 

                $this->set('json_content', json_encode(array('downloadToken' => $this->Session->read('Generer.downloadToken'))));
                $this->layout = NULL;
                $this->render('/Layouts/json');
            }
        }
        
        
        function getGeneration(){
            $listFiles = $this->Session->read('tmp.listFiles');
            $this->Session->delete('tmp.listFiles');
            $format = $this->Session->read('tmp.format');
            $this->Session->delete('tmp.format');
            
            $this->set('listFiles', $listFiles);
            $this->set('format', $format);
            //FIXME remplacer par previous ?
            $lastUrl = $this->Session->read('user.User.lasturl');
            if (strpos($lastUrl, 'getGeneration'))
                $lastUrl = $this->previous;
            $this->set('urlRetour', $lastUrl);
            $this->render('generer');
        }

	function _sendDocument($acteur, $fichier, $path) {
		if (($this->Session->read('user.format.sortie')==0) )
			$format    = ".pdf";
		else
			$format    = ".odt";

		if ($acteur['email'] != '') {
			if (Configure::read("SMTP_USE")) {
				$this->Email->smtpOptions = array( 'port'     => Configure::read("SMTP_PORT"),
						'timeout'  => Configure::read("SMTP_TIMEOUT"),
						'host'     => Configure::read("SMTP_HOST"),
						'username' => Configure::read("SMTP_USERNAME"),
						'password' => Configure::read("SMTP_PASSWORD"),
						'client'   => Configure::read("SMTP_CLIENT")
				);
				$this->Email->delivery = 'smtp';
			}
			else
				$this->Email->delivery = 'mail';

			$this->Email->from = Configure::read("MAIL_FROM");
			$this->Email->to = $acteur['email'];
			$this->Email->charset = 'UTF-8';

			$this->Email->subject = utf8_encode("Vous venez de recevoir un document de Webdelib ");
			$this->Email->sendAs = 'text';
			$this->Email->layout = 'default';
			$this->Email->template = 'convocation';
			$this->set('data',   $this->paramMails('convocation',  $acteur ));

			$this->Email->attachments = array($path.$fichier.$format);
			$this->Email->send();
		}
	}

	function paramMails($type,  $acteur) {
		$handle  = fopen(CONFIG_PATH.'/emails/'.$type.'.txt', 'r');
		$content = fread($handle, filesize(CONFIG_PATH.'/emails/'.$type.'.txt'));
		$searchReplace = array(
				"#NOM#" => $acteur['nom'],
				"#PRENOM#" => $acteur['prenom'],
		);
		return utf8_encode(nl2br((str_replace(array_keys($searchReplace), array_values($searchReplace), $content))));
	}

	function changeStatus($field, $id) {

             $data = $this->Modeledition->find('first', array('conditions' => array("Modeledition.id" => $id),
                                               'recursive'  => -1,
                                               'fields'     => array("$field")));
             $this->Modeledition->id = $id;
             if ($data['Modeledition'][$field] == 0)
                 $this->Modeledition->saveField($field, 1);
             elseif($data['Modeledition'][$field] == 1)
                 $this->Modeledition->saveField($field, 0);

            $this->Session->setFlash('Modification prise en compte', 'growl');
	    $this->redirect('/models/index');

	}

}
