<?php
    echo $this->Html->script('utils');
?>
<h2>Import du modèle : <?php echo $libelle; ?></h2>
<div class="optional">
<?php
    echo $this->Form->create('Model',array('url'=>'/models/import/'.$model_id,'type'=>'file'));
    echo $this->Form->input("template",array('label'=>false, 'type'=>'file', 'div'=>false));
?>
</div>
<div class="submit">
<?php 
    echo $this->Form->button('<i class="icon-download"></i> Importer', array('div'=>false, 'class'=>'btn btn-primary', 'name'=>'importer', 'type'=>'submit'));
?>
</div>
<?php echo $this->Form->end(); ?>