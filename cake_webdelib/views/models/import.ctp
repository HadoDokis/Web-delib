<?php
    echo $javascript->link('fckeditor/fckeditor');
    echo $javascript->link('utils');
?>
<h2>Import du mod&egrave;le : <?php echo $libelle; ?></h2>
<div class="optional">
<?php
	echo $form->create('Model',array('url'=>'/models/import/'.$model_id,'type'=>'file'));
    if (Configure::read('USE_GEDOOO')){
        echo  $form->input("template",array('label'=>'', 'type'=>'file', 'div'=>false));
    }
    else {
        echo $form->input('Model.content', array('type'=>'textarea', 'cols' => '10', 'rows' => '20'));
        echo $fck->load('data[Model][content]');
   }
   echo  $form->input("Model.recherche");
?>
</div>
<div class="submit">
<?php 
    echo $form->submit('Importer', array('div'=>false, 'class'=>'bt_add', 'name'=>'importer'));
    echo '<br><br>';
?>
</div>
<?php echo $form->end(); ?>
