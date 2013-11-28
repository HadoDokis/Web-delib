<h2>Avancer le projet à une étape suivante</h2>

<?php
echo $this->Form->create('Traitement', array('url'=>array('controller'=>'deliberations', 'action'=>'goNext', $delib_id)));
    echo $this->Form->input('etape', array('label'=>'Destinataire', 'title'=>"A qui voulez vous envoyer le projet ?"));
    echo '<br/>';
    echo '<div class="submit btn-group">';
        echo $this->Html->link('<i class=" fa fa-arrow-left"></i> Annuler', array('action'=>'tousLesProjetsValidation'), array('class'=>'btn', 'name'=>'Annuler', 'escape'=>false));
        echo $this->Form->button('<i class="fa fa-check"></i> Valider', array('div'=>false, 'class'=>'btn btn-primary', 'name'=>'Valider', 'type'=>'submit'));
    echo '</div>';
echo $this->Form->end();
?>
<style>
    label {
        float:none;
        text-align: left;
    }
</style>