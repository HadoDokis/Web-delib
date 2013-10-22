<h2>Liste des profils des utilisateurs</h2>
<div id="arbre">
	<?php echo $this->Tree->showTree('Profil', 'libelle', $data, 0, $this->base, array('Editer'=>'edit', 'Supprimer'=>'delete', 'Envoyer un mail' => 'notifier')); ?>
</div>
<div>
    <?php $this->Html2->boutonAdd("Ajouter un profil", 'Ajouter'); ?>
</div>

