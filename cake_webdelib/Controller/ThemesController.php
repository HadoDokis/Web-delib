<?php

class ThemesController extends AppController {

	var $name = 'Themes';
	var $helpers = array('Html', 'Form', 'Session', 'Tree');
	var $components = array('Droits');

	// Gestion des droits
	var $aucunDroit = array(
			'getLibelle',
			'isEditable',
			'view'
	);
	var $commeDroit = array(
			'edit'=>'Themes:index',
			'add'=>'Themes:index',
			'delete'=>'Themes:index'
	);

	function getLibelle ($id = null) {
		$condition = "Theme.id = $id";
		$objCourant = $this->Theme->find('first', array(
				'conditions' => array('Theme.id' => $id),
				'recursive'  => -1,
				'fields'     => array('libelle')));
		return $objCourant['Theme']['libelle'];
	}

	function index() {
		$themes =  $this->Theme->find('threaded',array('conditions'=>array('actif'=>1),
				'order'=>'Theme.order ASC',
				'recursive'=>-1));
		$this->set('data', $themes);
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le Th&egrave;me.', 'growl',array('type'=>'erreur'));
			$this->redirect('/themes/index');
		}
		$this->set('theme', $this->Theme->read(null, $id));
		$this->set('user_id',$this->Session->read('user.User.id'));
		$this->set('Droits',$this->Droits);
	}

	function add() {
		$themes = $this->Theme->generateTreeList(array('Theme.actif' => '1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;');
		$this->set('themes', $themes);
		if (!empty($this->data)) {
			if (empty($this->data['Theme']['parent_id'])) 
				$this->request->data['Theme']['parent_id']=0;
			$this->request->data['Theme']['actif'] =1;

			if ($this->Theme->save($this->data)) {
				$this->Session->setFlash('Le th&egrave;me a &eacute;t&eacute; sauvegard&eacute;', 'growl');
				$this->redirect('/themes/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl',array('type'=>'erreur'));
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour le Th&egrave;me', 'growl',array('type'=>'erreur'));
				$this->redirect('/Themes/index');
			}
			$this->data = $this->Theme->read(null, $id);
			$themes = $this->Theme->generateTreeList(array('Theme.id <>'=> $id,'Theme.actif' => '1'), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;');
			$this->set('isEditable', $this->isEditable($id));
			$this->set('themes', $themes);
			$this->set('selectedTheme',$this->data['Theme']['parent_id']);
		} else {
			if (empty($this->data['Theme']['parent_id'])) $this->request->data['Theme']['parent_id']=0;
			if ($this->Theme->save($this->data)) {
				$this->Session->setFlash('Le th&egrave;me a &eacute;t&eacute; modifi&eacute;', 'growl');
				$this->redirect('/themes/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl',array('type'=>'erreur'));
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour le Th&egrave;me', 'growl',array('type'=>'erreur'));
			$this->redirect('/themes/index');
		}
		$theme = $this->Theme->read(null, $id);
		$theme['Theme']['actif'] = 0;
		if ( $this->Theme->save( $theme)) {
			$this->Session->setFlash('Le Th&egrave;me a &eacute;t&eacute; d&eacute;sactiv&eacute;', 'growl');
			$this->redirect('/themes/index');
		}
	}

	function isEditable ($id) {
		$liste = $this->Theme->find("first", array(
				'conditions' => array('Theme.parent_id' => $id),
				'recursive'  => -1));
		return empty($liste);
	}
}
?>
