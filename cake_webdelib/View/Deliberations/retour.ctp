<h2>Renvoyer le projet à une étape précédente</h2>

<?php
echo $this->Form->create('Traitement', array('url' => array('controller' => 'deliberations', 'action' => 'retour', $delib_id)));
echo $this->Form->input('etape', array('label' => 'Etape du circuit', 'title' => "A quelle étape voulez vous renvoyer le projet ?", 'style' => 'width: auto; max-width: 100%;'));
echo '<br/>';
echo '<div class="submit btn-group">';
echo $this->Html->link('<i class=" fa fa-arrow-left"></i> Annuler', array('action' => 'traiter', $delib_id), array('class' => 'btn', 'name' => 'Annuler', 'escape' => false));
echo $this->Form->button('<i class="fa fa-check"></i> Valider', array('div' => false, 'class' => 'btn btn-primary', 'name' => 'Valider', 'type' => 'submit'));
echo '</div>';
echo $this->Form->end();
?>
<script>
    $(document).ready(function(){
        $("#TraitementEtape").select2({
            width: 'auto'
        });
    })
</script>
<style>
    label {
        float:none;
        text-align: left;
    }
</style>