<h2>Liste des thèmes</h2>

<div id="arbre">
<?php echo $this->Tree->showTree('Theme','libelle', $data,0,$this->base, array('Editer'=>'edit','Supprimer'=>'delete'), 'order'); ?>
</div>

<div>
<?php echo $this->Html->link('Ajouter un thème', '/themes/add', array('class'=>'link_add', 'title'=>'Ajouter')); ?>
</div>
