<?php
class TypeacteursController extends AppController
{
	var $name = 'Typeacteurs';
        public $helpers = array();
        var $components = array(
            'Auth' => array(
            'mapActions' => array(
                'create' => array('admin_index','admin_add','admin_delete','admin_edit','admin_view')
            )
        ));

	function admin_index()
	{
            $this->set('typeacteurs', $this->Typeacteur->find('all', array('recursive' => -1,'order'=>'id ASC' )));
	}

	function admin_view($id = null) 	{
		$typeacteur = $this->Typeacteur->find('first', array('conditions' => array('Typeacteur.id'=> $id)));
		if (empty($typeacteur)) {
			$this->Session->setFlash('Invalide id pour le type d\'acteur', 'growl', array('type'=>'erreur'));
            $this->redirect(array('action'=>'index'));
		} else
			$this->set('typeacteur', $typeacteur);
	}

	function admin_add() {
            $sortie = false;
            if (!empty($this->request->data)) {
                    $this->Typeacteur->create($this->request->data);
                    if ($this->Typeacteur->save()) {
                            $this->Session->setFlash('Le type d\'acteur \''.$this->request->data['Typeacteur']['nom'].'\' a été ajouté', 'growl');
                            $sortie = true;
                    } else
                            $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl',array('type'=>'erreur'));
            }
            if ($sortie) {
                $this->redirect(array('action' => 'index'));
            } else {
                $this->set('eluNonElu', array('1' => 'élu', '0' => 'non élu'));
            }

            $this->render('admin_edit');
	}

    function admin_edit($id = null) {
        
        if ($this->request->isPost()) {
            if ($this->Typeacteur->save($this->request->data)) {
                $this->Session->setFlash('Le type d\'acteur \'' . $this->request->data['Typeacteur']['nom'] . '\' a été modifié', 'growl');
                
                $this->redirect($this->previous);
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
            }
        }   
        $this->Typeacteur->recursive = -1;
        $this->request->data=$this->Typeacteur->read( null, $id);

        if ($this->request->data==false) {
            $this->Session->setFlash('Invalide id pour le type d\'acteur', 'growl', array('type'=>'erreur'));
            $this->redirect($this->previous);
        }
            
        $this->set('eluNonElu', array('1' => 'élu', '0' => 'non élu'));
    }

	function admin_delete($id = null) {
		$typeacteur = $this->Typeacteur->read('id, nom', $id);
		if (empty($typeacteur)) {
			$this->Session->setFlash('Invalide id pour le type d\'acteur', 'growl', array('type'=>'erreur'));
		}
		elseif (!empty($typeacteur['Acteur'])) {
			$this->Session->setFlash('Le type d\'acteur \''.$typeacteur['Typeacteur']['nom'].'\' est utilisé par un acteur. Suppression impossible.', 'growl', array('type'=>'erreur'));
		}
		elseif ($this->Typeacteur->delete($id)) {
			$this->Session->setFlash('Le type d\'acteur \''.$typeacteur['Typeacteur']['nom'].'\' a été supprimé', 'growl');
		}
        $this->redirect(array('action'=>'index'));
	}
}
