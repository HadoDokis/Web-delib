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
    foreach ($user['Service'] as $service)
        if (is_array($service))
            $services.=$service['name'] . $this->Html->tag(null, '<br />');
    echo $this->Bs->tableCells(array(
        $user['User']['username'],
        $user['User']['nom'],
        $user['User']['prenom'],
        $user['Profil']['name'],
        $services,
        $this->Html->nestedList($user['Typeacte']),
        $this->Bs->div('btn-group') .
        $this->Bs->btn(null, array('controller' => 'users', 'action' => 'view', $user['User']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => 'Voir')) .
        $this->Bs->btn($this->Bs->icon('lock'), array('controller' => 'users', 'action' => 'changeMdp', $user['User']['id']), array('type' => 'default', 'title' => 'Nouveau mot de passe','escape'=>false)) .
        $this->Bs->btn(null, array('controller' => 'users', 'action' => 'edit', $user['User']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
        $this->Bs->btn(null, array('controller' => 'users', 'action' => 'delete', $user['User']['id']), array('type' => 'danger', 'icon' => 'glyphicon glyphicon-trash', 'title' => 'Supprimer', 'class' => !$user['User']['is_deletable'] ? 'disabled' : ''), 'Êtes vous sur de vouloir supprimer :' . $user['User']['username'] . ' ?') .
        $this->Bs->close()
    ));
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