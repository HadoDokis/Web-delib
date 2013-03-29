<div id="vue_cadre">
<?php
    echo $this->Form->create('Nature',array('url'=>'/natures/add/','type'=>'post'));
    echo $this->Form->input('libelle',  array('label'=>"Libelle : "));
    $this->Html2->boutonSubmit();
    echo $this->Form->end(); 
?>
</div>
