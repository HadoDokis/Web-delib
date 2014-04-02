<h2>Liste des thèmes</h2>

<div id="arbre">
<?php echo $this->Tree->showTree('Theme','libelle', $data,0,$this->base, array('Editer'=>'edit','Supprimer'=>'delete'), 'order'); ?>
</div>

<div>
<?php $this->Html2->boutonAdd(); ?>
</div>
