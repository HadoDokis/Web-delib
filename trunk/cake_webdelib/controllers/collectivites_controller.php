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
				$this->redirect('/collectivites');
		}	
 	}
 	
 	function setLogo($id = null) {
 		if (empty($this->data)) {
			$this->data = $this->Collectivite->read(null, $id);
		}
		else {
		    $type_file = $this->data['Image']['logo']['type'];
            if( !strstr($type_file, 'jpg') && !strstr($type_file, 'jpeg') && !strstr($type_file, 'bmp') && !strstr($type_file, 'gif') && !strstr($type_file, 'png')){
                exit("Le fichier n'est pas une image");
    		}
			$name_file = 'logo.jpg';
			$content_dir = '/home/francois/.workspace/svn_webdelib/webroot/files/image/';
			$tmp_file =  $this->data['Image']['logo']['tmp_name'];
						
    		if( !move_uploaded_file($tmp_file, 	$content_dir.$name_file) ){
       		    exit("Impossible de copier le fichier dans $content_dir");
   			 }
			$this->redirect('/collectivites');
		}
 	}
}
 
 
 
?>