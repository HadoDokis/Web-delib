<?php

echo $this->Bs->tag('h3', 'Liste des utilisateurs') .
        $this->element('filtre').
 $this->Bs->table(array(array('title' => $this->Paginator->sort('login', 'Login')),
    array('title' => $this->Paginator->sort('nom', 'Nom')),
    array('title' => $this->Paginator->sort('prenom', 'Prénom')),
    array('title' => $this->Paginator->sort('Profil.libelle', 'Profil')),
    array('title' => 'Services'),
    array('title' => 'Types d\'actes'),
    array('title' => 'Actions'),
        ), array('hover', 'striped'));
foreach ($users as $user) {
    $services='';
    foreach ($user['Service'] as $service)
        if (is_array($service))
            $services.=$service['libelle'] . $this->Html->tag(null, '<br />');
        $natures='';
        if(!empty($user['Service']))
        foreach ($user['Natures'] as $nature)
            $natures.=$nature . $this->Html->tag(null, '<br />');                   
    echo $this->Bs->tableCells(array(
        $user['User']['login'],
        $user['User']['nom'],
        $user['User']['prenom'],
        $user['Profil']['libelle'],
        $services,
        $natures,
        $this->Bs->div('btn-group') .
        $this->Bs->btn(null, array('controller' => 'users', 'action' => 'view', $user['User']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => 'Voir')) .
        $this->Bs->btn($this->Bs->icon('lock'), array('controller' => 'users', 'action' => 'changeMdp', $user['User']['id']), array('type' => 'default', 'title' => 'Nouveau mot de passe','escape'=>false)) .
        $this->Bs->btn(null, array('controller' => 'users', 'action' => 'edit', $user['User']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
        $this->Bs->btn(null, array('controller' => 'users', 'action' => 'delete', $user['User']['id']), array('type' => 'danger', 'icon' => ' glyphicon glyphicon-trash', 'title' => 'Supprimer', 'class' => !$user['User']['is_deletable'] ? 'disabled' : ''), 'Êtes vous sur de vouloir supprimer :' . $user['User']['login'] . ' ?') .
        $this->Bs->close()
    ));
}
echo $this->Bs->endTable() .
 $this->Html2->btnAdd("Ajouter un utilisateur", "Ajouter");
?>

    <div class='paginate'>
        <!-- Affiche les numéros de pages -->
        <?php echo $this->Paginator->numbers(); ?>
        <!-- Affiche les liens des pages précédentes et suivantes -->
        <?php
        echo $this->Paginator->prev('« Précédent ', null, null, array('tag' => 'span', 'class' => 'disabled'));
        echo $this->Paginator->next(' Suivant »', null, null, array('tag' => 'span', 'class' => 'disabled'));
        ?>
        <!-- Affiche X de Y, où X est la page courante et Y le nombre de pages -->
        <?php echo $this->Paginator->counter(array('format' => 'Page %page% sur %pages%')); ?>
    </div>