<?php
class CompteursController extends AppController
{
	var $name = 'Compteurs';
	var $components = array('Security');

	// Gestion des droits
	var $demandePost = array('suivant');
	var $commeDroit = array('edit'=>'Compteurs:index', 'view'=>'Compteurs:index');

	function beforeFilter()
    {
    	if (property_exists($this, 'demandePost'))
			call_user_func_array(array($this->Security, 'requirePost'), $this->demandePost);
    }

/**
* Retourne la valeur suivante du compteur,
* enregistre la nouvelle valeur de la séquence et du critère de réinitialisation en base
*
* @param int $id Numéro de l'id du compteur
* @retourne string Valeur suivante du compteur
* @access public
*/
	function suivant($id = null)
	{
		/* initialisations */
		if (!$id) {
			$id = $this->id;
		}

		/* initialisation du tableau de recherche et de remplacement pour la date */
		$timestamp = time();
		$remplaceD = array("#AAAA#" => date("Y", $timestamp)
						, "#AA#" => date("y", $timestamp)
						, "#M#" => date("n", $timestamp)
						, "#MM#" => date("m", $timestamp)
						, "#J#" => date("j", $timestamp)
						, "#JJ#" => date("d", $timestamp)
						);

		/* lecture du compteur en base */
		$cptEnBase = $this->Compteur->read('def_reinit, val_reinit, num_sequence, def_compteur', $id);

		/* génération du critère de réinitialisation courant */
		$val_reinitCourant = str_replace(array_keys($remplaceD), array_values($remplaceD), $cptEnBase['Compteur']['def_reinit']);

		/* traitement de la séquence */
		if ($val_reinitCourant != $cptEnBase['Compteur']['val_reinit'])
		{
			$cptEnBase['Compteur']['num_sequence'] = 1;
			$cptEnBase['Compteur']['val_reinit'] = $val_reinitCourant;
		} else
		{
			$cptEnBase['Compteur']['num_sequence']++;
		}

		/* initialisation du tableau de recherche et de remplacement pour la séquence */
		$strnseqS = sprintf("%10d", $cptEnBase['Compteur']['num_sequence']);
		$strnseqZ = sprintf("%010d", $cptEnBase['Compteur']['num_sequence']);

		$remplaceS = array("#s#" => $cptEnBase['Compteur']['num_sequence']
						, "#S#" => substr($strnseqS, -1, 1)
						, "#SS#" => substr($strnseqS, -2, 2)
						, "#SSS#" => substr($strnseqS, -3, 3)
						, "#SSSS#" => substr($strnseqS, -4, 4)
						, "#SSSSS#" => substr($strnseqS, -5, 5)
						, "#SSSSSS#" => substr($strnseqS, -6, 6)
						, "#SSSSSSS#" => substr($strnseqS, -7, 7)
						, "#SSSSSSSS#" => substr($strnseqS, -8, 8)
						, "#SSSSSSSSS#" => substr($strnseqS, -9, 9)
						, "#SSSSSSSSSS#" => $strnseqS
						, "#0#" => substr($strnseqZ, -1, 1)
						, "#00#" => substr($strnseqZ, -2, 2)
						, "#000#" => substr($strnseqZ, -3, 3)
						, "#0000#" => substr($strnseqZ, -4, 4)
						, "#00000#" => substr($strnseqZ, -5, 5)
						, "#000000#" => substr($strnseqZ, -6, 6)
						, "#0000000#" => substr($strnseqZ, -7, 7)
						, "#00000000#" => substr($strnseqZ, -8, 8)
						, "#000000000#" => substr($strnseqZ, -9, 9)
						, "#0000000000#" => $strnseqZ
						);

		/* génération de la valeur du compteur */
		$valCompteurD = str_replace(array_keys($remplaceD), array_values($remplaceD), $cptEnBase['Compteur']['def_compteur']);
		$valCompteur = str_replace(array_keys($remplaceS), array_values($remplaceS), $valCompteurD);

		/* Sauvegarde du compteur en base */
		$this->Compteur->save($cptEnBase, true, array('num_sequence', 'val_reinit'));

		/* retourne la valeur du compteur générée */
		return $valCompteur;
	}

	function index()
	{
		$this->set('compteurs', $this->Compteur->findAll());
	}

	function view($id = null)
	{
		$this->set('compteur', $this->Compteur->read(null, $id));
	}

	function edit($id = null)
	{
		if (empty($this->data))
		{
			$this->data = $this->Compteur->read(null, $id);
		} else
		{
			$this->cleanUpFields();
			if ($this->Compteur->save($this->data))
			{
				$this->Session->setFlash('Le compteur a &eacute;t&eacute; modifi&eacute;');
				$this->redirect('/compteurs/index');
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
		}
	}

}
?>