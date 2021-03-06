<?php
class CommentairesController extends AppController
{

    public function add($delib_id = null)
    {
        if (empty($delib_id)) {
            $this->Session->setFlash('Identifiant de délibération introuvable.', 'growl');
            return $this->redirect($this->previous);
        }
        $this->set('delib_id', $delib_id);
        if (!empty($this->request->data)) {
            $this->Commentaire->create();
            $this->request->data['Commentaire']['agent_id'] = $this->Auth->user('id');
            $this->request->data['Commentaire']['commentaire_auto'] = 0;
            if ($this->Commentaire->save($this->request->data)) {
                return $this->redirect($this->previous);
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
            }
        }
        $this->set('retour', $this->previous);
    }

    public function edit($id = null, $delib_id = null)
    {
        if (empty($this->request->data)) {
            if (empty($id)) {
                $this->Session->setFlash('Invalide id pour le commentaire', 'growl');
                return $this->redirect($this->previous);
            }
            $this->set('delib_id', $delib_id);
            $this->request->data = $this->Commentaire->read(null, $id);
        } else {
            $this->request->data['Commentaire']['agent_id'] = $this->Session->read('user.User.id');
            if ($this->Commentaire->save($this->request->data)) {
                return $this->redirect($this->previous);
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
            }
        }
    }

    public function delete($id = null)
    {
        if (!$id) {
            $this->Session->setFlash('Invalide id pour le commentaire', 'growl');
            $this->redirect($this->previous);
        }
        if ($this->Commentaire->delete($id)) {
            $this->Session->setFlash('Commentaire supprimé !', 'growl');
            $this->redirect($this->previous);
        }
    }

    public function prendreEnCompte($id = null, $delib_id)
    {
        if (!$id) {
            $this->Session->setFlash('Invalide id pour le commentaire', 'growl');
            $this->redirect(array('controller' => 'deliberations', 'action' => 'traiter', $delib_id));
        }
        $this->request->data = $this->Commentaire->read(null, $id);
        $this->request->data['Commentaire']['pris_en_compte'] = 1;
        if ($this->Commentaire->save($this->request->data)) {
            $this->redirect(array('controller' => 'deliberations', 'action' => 'traiter', $delib_id));
            $this->Session->setFlash('Commentaire pris en compte', 'growl');
        }
    }

    public function RetourTraiter($delib_id = null)
    {
        $this->Session->setFlash('Invalide id pour le commentaire.', 'growl');
        if (!$delib_id) {
            $this->redirect('/');
        } else {
            $this->redirect(array('controller' => 'deliberations', 'action' => 'traiter', $delib_id));
        }
    }

}
