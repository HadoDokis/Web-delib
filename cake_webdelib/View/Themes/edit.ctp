<h2>Modification d'un thème</h2>
<?php echo $this->Form->create('Theme', array('url' => '/themes/edit/'.$this->Html->value('Service.id'),'type'=>'post')); ?>
<div class="optional">
 	<?php echo $this->Form->input('Theme.libelle', array('label'=>'Libellé','size' => '60', 'maxlength' => '500'));?>
</div>
<div class="optional">
 	<?php echo $this->Form->input('Theme.order', array('label'=>'Crit&egrave;re de tri','size' => '10'));?>
</div>
<div>
	<?php
	     echo $this->Form->input('Theme.parent_id', array('label'=>'Appartient &agrave;', 'options'=>$themes, 'default'=>$selectedTheme, 'empty'=>'', 'escape'=>false));
	?>
</div>
<div class="submit">
    <?php 
        echo $this->Form->hidden('Theme.id');
        $this->Html2->boutonsSaveCancel();
    ?>
</div>
<?php echo $this->Form->end(); ?>
