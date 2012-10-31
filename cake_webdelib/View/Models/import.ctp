<?php
    echo $this->Html->script('utils');
?>
<h2>Import du mod&egrave;le : <?php echo $libelle; ?></h2>
<div class="optional">
<?php
    echo $this->Form->create('Model',array('url'=>'/models/import/'.$model_id,'type'=>'file'));
    echo  $this->Form->input("template",array('label'=>'', 'type'=>'file', 'div'=>false));
?>
</div>
<div class="submit">
<?php 
    echo '<br><br>';
    echo $this->Form->submit('Importer', array('div'=>false, 'class'=>'bt_add', 'name'=>'importer'));
    echo '<br><br>';
?>
</div>
<?php echo $this->Form->end(); ?>
