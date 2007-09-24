<?php
/*
 * Created on 24 sept. 07
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class CollectivitesController extends AppController {
 	

	function index() {
		$this->Seance->recursive = 0;
		$this->set('collectivite', $this->Collectivite->findAll());
	}
 	
 	function edit($id = null) {
		if (empty($this->data)) {
			$this->data = $this->Collectivite->read(null, $id);
		}
		else {
			if(!empty($this->params['form']))
				$this->Collectivite->save($this->data);
		}
 
 	}
}
 
 
 
?>