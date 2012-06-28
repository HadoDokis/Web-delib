<div id="vue_cadre">

<?php
    echo $form->create('Nature',array('url'=>'/natures/add/','type'=>'post'));
    echo $form->input('libelle',  array('label'=>"Libelle : "));
    echo $form->submit('Enregistrer',array('div'=>false));
    echo $form->end(); 
?>
</div>
