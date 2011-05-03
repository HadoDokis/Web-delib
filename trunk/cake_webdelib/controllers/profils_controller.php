<?php
class ProfilsController extends AppController {

	var $name = 'Profils';
	var $helpers = array('Session', 'Tree');
	var $components = array('Dbdroits', 'Menu');
	var $uses = array('Profil', 'Aro');

	// Gestion des droits
	var $aucunDroit = array('changeParentId');
	var $commeDroit = array(
		'add'=>'Profils:index',
		'delete'=>'Profils:index',
		'edit'=>'Profils:index',
		'view'=>'Profils:index'
		);

		function index() {
			$profils = $this->Profil->find('threaded',array('order'=>'Profil.id ASC','recursive'=>-1));
			$this->_isDeletable($profils);
			$this->set('data', $profils);
		}

		function _isDeletable(&$profils) {
			foreach($profils as &$profil) {
				if ($this->Profil->User->find('first', array('conditions'=>array('User.profil_id'=>$profil['Profil']['id']),'recursive'=>-1)))
				$profil['Profil']['deletable'] = false;
				else
				$profil['Profil']['deletable'] = true;
				if ($profil['children'] != array())
				$this->_isDeletable($profil['children']);
			}
		}

		function view($id = null) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour le profil.');
				$this->redirect('/profils/index');
			}
			$this->set('profil', $this->Profil->read(null, $id));
		}

		function add() {
			$sortie = false;
			if (!empty($this->data)) {
				if (empty($this->data['Profil']['parent_id'])) $this->data['Profil']['parent_id']=0;
				if ($this->Profil->save($this->data)) {
					if ($this->data['Profil']['parent_id']!=0) {
						$this->data['Droits'] = $this->Dbdroits->litCruDroits(array('model'=>'Profil','foreign_key'=>$this->data['Profil']['parent_id']));
						$this->Dbdroits->MajCruDroits(
						array(
							'model'=>'Profil',
							'foreign_key'=>$this->Profil->id,
							'alias'=>$this->data['Profil']['libelle']
						),
						array (
							'model'=>'Profil',
							'foreign_key'=>$this->data['Profil']['parent_id']
						),
						$this->data['Droits']
						);
					}
					else {
						$this->Dbdroits->AddCru(
						array(
							'model'=>'Profil',
							'foreign_key'=>$this->Profil->id,
							'alias'=>$this->data['Profil']['libelle']
						),
						null
						);
					}
					$this->Session->setFlash('Le profil a &eacute;t&eacute; sauvegard&eacute;');
					$sortie = true;
				} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
			if ($sortie)
			$this->redirect('/profils/index');
			else {
				$this->set('profils', $this->Profil->find('list',array('order'=>array('Profil.libelle ASC'))));
			}
		}

		function edit($id = null) {
			$sortie = false;
			if (empty($this->data)) {
				$this->data = $this->Profil->find('first', array('conditions' => array("Profil.id" => $id)));
				if (empty($this->data)) {
					$this->Session->setFlash('Invalide id pour le profil');
					$sortie = true;
				}
				$this->data['Droits'] = $this->Dbdroits->litCruDroits(array('model'=>'Profil','foreign_key'=>$id));

			} else {
                                require_once ('vendors/progressbar.php');
                                Initialize(200, 100,200, 30,'#000000','#FFCC00','#006699');
				if (empty($this->data['Profil']['parent_id'])) $this->data['Profil']['parent_id']=0;
				$profil=$this->Profil->read(null,$id);
				if ($this->Profil->save($this->data)) {
					if ($profil['Profil']['parent_id']!=$this->data['Profil']['parent_id']) {
						$this->data['Droits'] = $this->Dbdroits->litCruDroits(array('model'=>'Profil','foreign_key'=>$this->data['Profil']['parent_id']));
						$this->Dbdroits->MajCruDroits(
						array('model'=>'Profil','foreign_key'=>$this->data['Profil']['id'],'alias'=>$this->data['Profil']['libelle']),
						array('model'=>'Profil','foreign_key'=>$this->data['Profil']['parent_id']),
						$this->data['Droits']
						);
					}
					elseif ($profil['Profil']['parent_id']!=0) {
						$this->Dbdroits->MajCruDroits(
						array('model'=>'Profil','foreign_key'=>$this->data['Profil']['id'],'alias'=>$this->data['Profil']['libelle']),
						array('model'=>'Profil','foreign_key'=>$this->data['Profil']['parent_id']),
						$this->data['Droits']
						);
					}
					else {
						$this->Dbdroits->MajCruDroits(
						array('model'=>'Profil','foreign_key'=>$this->data['Profil']['id'],'alias'=>$this->data['Profil']['libelle']),
						null,
						$this->data['Droits']
						);
					}
					$aro_id = $this->Aro->find('first',array('conditions'=>array('model'=>'Profil', 'foreign_key'=>$id),'fields'=>array('id')));
					$this->Aro->id = $aro_id['Aro']['id'];
					$this->Aro->saveField('parent_id',$this->data['Profil']['parent_id']);
					$Users=$this->Profil->User->find('all',array('conditions'=>array('User.profil_id'=>$this->data['Profil']['id']),'recursive'=>-1));
                                        $nbUsers = count($Users);
                                        $cpt = 0;
					foreach($Users as $User) {
                                            $cpt++;
                                            ProgressBar($cpt*(100/$nbUsers), 'Mise à jour des données pour : <b>'. $User['User']['login'].'</b>');
			                    $this->Dbdroits->MajCruDroits(
						array(
							'model'=>'Utilisateur','foreign_key'=>$User['User']['id'],'alias'=>$User['User']['login']),
						array(
							'model'=>'Profil','foreign_key'=>$User['User']['profil_id']),
						$this->data['Droits']
						);
					}
                                        die ("sdfsdfqsdfqdsfqsdf");
					$this->Session->setFlash('Le profil a &eacute;t&eacute; modifi&eacute;');
					$sortie = true;
				} else
				    $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
			}
			if ($sortie) {
                              echo ('<script>');
                              echo ('    document.getElementById("pourcentage").style.display="none"; ');
                              echo ('    document.getElementById("progrbar").style.display="none";');
                              echo ('    document.getElementById("affiche").style.display="none";');
                              echo ('    document.getElementById("contTemp").style.display="none";');
                              echo ('</script>');
                              die ('<br /><a href ="/profils/index"> Retour &agrave; la page pr&eacute;c&eacute;dente </a>');

                        }
			else {
				$this->set('profils', $this->Profil->find('list',array('conditions'=>array("Profil.id <>"=>"$id"),'order'=>array('Profil.libelle ASC'))));
				$this->set('selectedProfil',$this->data['Profil']['parent_id']);
				$this->set('listeCtrlAction', $this->Menu->menuCtrlActionAffichage());
			}

		}

		function delete($id = null) {
			if (!$id) {
				$tab = $this->Profil->findAll("Profil.id=$id");
				$this->Session->setFlash('Invalide id pour le profil');
				$this->redirect('/profils/index');
			}
			if (!$this->Profil->User->find('first', array('conditions'=>array('User.profil_id'=>$id)))) {
				if ($this->Profil->delete($id)) {
					$aro_id = $this->Aro->find('first',array('conditions'=>array('model'=>'Profil', 'foreign_key'=>$id),'fields'=>array('id')));
					$this->Aro->delete($aro_id['Aro']['id']);
					$this->Session->setFlash('Le profil a &eacute;t&eacute; supprim&eacute;');
					$this->redirect('/profils/index');
				}
				else {
					$this->Session->setFlash('Impossible de supprimer ce profil');
					$this->redirect('/profils/index');
				}
			}
			else {
				$this->Session->setFlash('Impossible de supprimer ce profil car il est attribu�.');
				$this->redirect('/profils/index');
			}
		}

		function changeParentId($curruentParentId, $newParentId) {
			$this->data = $this->Profil->findByParentId(null, $id);
			debug($this->data);exit;
		}

}
?>
