<div id="vue_cadre">

<?php
    echo $this->Form->create('Nature',array('url'=>'/natures/add/','type'=>'post'));
    echo $this->Form->input('libelle',  array('label'=>"Libelle : "));
    echo $this->Form->submit('Enregistrer',array('div'=>false));
    echo $this->Form->end(); 
?>
</div>
