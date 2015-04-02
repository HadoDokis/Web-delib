<?php
$this->Html->addCrumb('Liste des utilisateurs');

echo $this->Bs->tag('h3', 'Liste des utilisateurs') .
        $this->element('filtre').
 $this->Bs->table(array(array('title' => $this->Paginator->sort('username', 'Login')),
    array('title' => $this->Paginator->sort('nom', 'Nom')),
    array('title' => $this->Paginator->sort('prenom', 'Prénom')),
    array('title' => $this->Paginator->sort('Profil.name', 'Profil')),
    array('title' => 'Services'),
    array('title' => 'Types d\'actes'),
    array('title' => 'Actions'),
        ), array('hover', 'striped'));
foreach ($users as $user) {
    $services='';
    $user_actif = $user['User']['active'];
    foreach ($user['Service'] as $service){
        if (is_array($service)){
            $services.=$service['name'] . $this->Html->tag(null, '<br />');
        }
    }
    if (!$user_actif){ echo $this->Bs->lineColor('danger'); }
    echo $this->Bs->cell($user['User']['username']) .
    $this->Bs->cell($user['User']['nom']) .
    $this->Bs->cell($user['User']['prenom']) .
    $this->Bs->cell($user['Profil']['name']) .
    $this->Bs->cell($services) .
    $this->Bs->cell($this->Html->nestedList($user['Typeacte'])) .
    $this->Bs->cell(
        $this->Bs->div('btn-group') .
        $this->Bs->btn(null, array('controller' => 'users', 'action' => 'view', $user['User']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => 'Voir')) .
        $this->Bs->btn($this->Bs->icon('lock'), array('controller' => 'users', 'action' => 'changeMdp', $user['User']['id']), array('type' => 'default', 'title' => 'Nouveau mot de passe','escape'=>false)) .
        $this->Bs->btn(null, array('controller' => 'users', 'action' => 'edit', $user['User']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
        $this->Bs->btn(null, array('controller' => 'users', 'action' => 'delete', $user['User']['id']), array('type' => 'danger', 'icon' => 'glyphicon glyphicon-trash', 'title' => 'Supprimer', 'class' => !$user['User']['is_deletable'] ? 'disabled' : ''), 'Êtes vous sur de vouloir supprimer : ' . $user['User']['username'] . ' ?') .
        $this->Bs->btn(null, array(
               'controller' => 'users', 
               'action' => ($user_actif)?'disable':'enable', 
               $user['User']['id']), array(
                   'type' => 'default', 
                   'icon' => ($user_actif)?'toggle-on':'toggle-off', 
                   'title' => ($user_actif)?'Désactiver':'Activer'),
                   'Êtes vous sur de vouloir '.(($user_actif)?'désactiver':'activer').' : ' . $user['User']['username'] . ' ?') .
    $this->Bs->close());

}
echo $this->Bs->endTable() .
        $this->Paginator->numbers(array(
    'before' => '<ul class="pagination">',
    'separator' => '',
   'currentClass' => 'active',
    'currentTag' => 'a',
    'tag' => 'li',
    'after' => '</ul><br />'
)).
 $this->Html2->btnAdd("Ajouter un utilisateur", "Ajouter");