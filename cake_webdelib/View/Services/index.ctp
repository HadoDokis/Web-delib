<h2>Liste des services</h2>

<div id="arbre">
<?php echo $this->Tree->showTree('Service','libelle', $data,0,$this->base, array('Editer'=>'edit','Supprimer'=>'delete'), 'order'); ?>
</div>

<div>
<?php $this->Html2->boutonAdd("Ajouter un service", "Ajouter"); ?>
</div>
