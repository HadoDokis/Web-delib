<?php
class InfosupsController extends AppController
{
	var $name = 'Infosups';

	// Gestion des droits ?
	var $aucunDroit;

	function download($foreignKey = 0, $infosupdef_id = 0) {
		/* lecture en base */
		$infosup = $this->Infosup->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'foreign_key' => $foreignKey,
				'infosupdef_id' => $infosupdef_id)));
		if (empty($infosup))
			$this->redirect($this->referer());
		else {
			header('Content-type: '.$infosup['Infosup']['file_type']);
			header('Content-Length: '.$infosup['Infosup']['file_size']);
			header('Content-Disposition: attachment; filename='.$infosup['Infosup']['file_name']);
			echo $infosup['Infosup']['content'];
			exit();
		}
	}
}
?>