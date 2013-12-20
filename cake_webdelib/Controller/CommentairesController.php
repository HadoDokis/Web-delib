<?php
class CommentairesController extends AppController
{
    // Gestion des droits
    public $aucunDroit;

    public function add($delib_id = null)
    {
        if (!$delib_id) {
            $this->Session->setFlash('Invalide id pour la délibération du commentaire.', 'growl');
            $this->redirect('/');
        }
        $this->set('delib_id', $delib_id);
        if (!empty($this->request->data)) {
            $this->Commentaire->create();
            $this->request->data['Commentaire']['agent_id'] = $this->Session->read('user.User.id');
            $this->request->data['Commentaire']['commentaire_auto'] = 0;
            if ($this->Commentaire->save($this->request->data)) {
                $this->redirect(array('controller' => 'deliberations', 'action' => 'traiter', $this->request->data['Commentaire']['delib_id']));
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
            }
        }
    }

    public function edit($id = null, $delib_id = null)
    {
        if (empty($this->request->data)) {
            if (!$id) {
                $this->Session->setFlash('Invalide id pour le commentaire', 'growl');
                $this->redirect(array('controller' => 'deliberations', 'action' => 'traiter', $delib_id));
            }
            $this->set('delib_id', $delib_id);
            $this->request->data = $this->Commentaire->read(null, $id);
        } else {
            $this->request->data['Commentaire']['agent_id'] = $this->Session->read('user.User.id');
            if ($this->Commentaire->save($this->request->data)) {
                $this->redirect(array('controller' => 'deliberations', 'action' => 'traiter', $this->request->data['Commentaire']['delib_id']));
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl', array('type' => 'erreur'));
            }
        }
    }

    public function delete($id = null, $delib_id)
    {
        if (!$id) {
            $this->Session->setFlash('Invalide id pour le commentaire', 'growl');
            $this->redirect(array('controller' => 'deliberations', 'action' => 'traiter', $delib_id));
        }
        if ($this->Commentaire->delete($id)) {
            $this->redirect(array('controller' => 'deliberations', 'action' => 'traiter', $delib_id));
        }
    }

    public function view($id = null, $delib_id = null)
    {
        if (!$id) {
            $this->Session->setFlash('Invalide id pour le commentaire', 'growl');
            $this->redirect(array('controller' => 'deliberations', 'action' => 'traiter', $delib_id));
        }
        $this->set('commentaire', $this->Commentaire->read(null, $id));
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
