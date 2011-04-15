<?php
class InfosupsController extends AppController
{
	var $name = 'Infosups';

	// Gestion des droits ?
	var $aucunDroit;

	function download($delib_id = 0, $infosupdef_id = 0) {
		/* lecture en base */
		$infosup = $this->Infosup->find('deliberation_id = '.$delib_id.' AND infosupdef_id = '.$infosupdef_id, null, null,-1);
		if (empty($infosup))
			$this->redirect('/');
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