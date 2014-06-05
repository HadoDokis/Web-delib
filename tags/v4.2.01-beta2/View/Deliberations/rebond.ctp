<h2>Envoyer le projet à un utilisateur</h2>

<?php
    $options = array(
        'detour' =>'Envoyer (sans retour) <i class="fa fa-mail-forward"></i>',
        'retour' => 'Aller-retour <i class="fa fa-retweet"></i>',
        'validation'=> 'Validation finale <i class="fa fa-legal"></i>');
    $attributes=array('legend'=>false, 'separator' => '<br class="spacer"/>', 'value' => 'retour');
    echo $this->Form->create('Insert', array('url'=>array('controller'=>'deliberations', 'action'=>'rebond', $delib_id),'type'=>'post'));
    echo $this->Form->input('user_id', array('label'=>array('text'=>'Destinataire','style'=>'padding-top: 5px;'), 'title'=>"A qui voulez vous envoyer le projet ? : "));
?>
<div class="spacer"></div>
<div>
<?php
    if ($typeEtape == CAKEFLOW_COLLABORATIF) {
        echo $this->Form->hidden('option', array('value'=>'retour'));
        echo $this->Form->radio('option_disabled', $options, array_merge($attributes, array('disabled'=>true)));
        echo '<div class="spacer"></div>';
        echo $this->Html->para('profil', 'Note : pour les étapes collaboratives (ET), l\'aller-retour est la seule possibilité.',array('style'=>'float: left;text-align: left;'));
    } else {
        echo $this->Form->radio('option', $options, $attributes);
    }
?>
</div>
<div class="spacer"></div>
<div class="submit btn-group">
<?php
    echo $this->Html->link('<i class=" fa fa-arrow-left"></i> Annuler', array('action' => 'traiter', $delib_id), array('class' => 'btn', 'name' => 'Annuler', 'escape' => false));
    echo $this->Form->button('<i class="fa fa-check"></i> Valider', array('div' => false, 'class' => 'btn btn-primary', 'name' => 'Valider', 'type' => 'submit'));
?>
</div>
<?php echo $this->Form->end(); ?>
<script type="application/javascript">
    $(document).ready(function(){
       $('#InsertUserId').select2({
           width: "400px",
           minimumInputLength: 1
       });
    });
</script>
