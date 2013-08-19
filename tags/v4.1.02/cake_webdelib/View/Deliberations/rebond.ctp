<h2>Envoyer le projet à un utilisateur</h2>

<?php
    $options = array('detour' =>'Envoyer (sans retour) : ', 'retour' => 'Aller-retour :', 'validation'=> 'Validation finale :');
    $attributes=array('legend'=>false, 'style'=>'float:right;', 'separator' => '<br/><br/>', 'value' => 'retour');

    echo $this->Form->create('Insert', array('url'=>'/deliberations/rebond/'.$delib_id,'type'=>'post'));
    echo $this->Form->input('user_id', array('label'=>'Destinataire', 'title'=>"A qui voulez vous envoyer le projet ? : "));
    echo '<br/>';
    if ($typeEtape == CAKEFLOW_COLLABORATIF) {
        echo ('<div style="width: 200px">');
        echo $this->Form->hidden('retour', array('value'=>1));
        echo $this->Form->radio('option', $options,   array_merge($attributes, array('readonly'=>'readonly')));
        echo ('</div>');
        echo '<br class="clear:both"/>'; echo '<br />';
        echo $this->Html->para('profil', 'Note : pour les étapes collaboratives (ET), l\'aller-retour est obligatoire.',array('style'=>'float: left;text-align: left;'));
    } else {
        echo ('<div style="width: 200px">');
        echo $this->Form->radio('option', $options, $attributes);
        echo ('</div>');
    }
?>
<br/> <br/> <br/>
<?php
        echo '<div class="submit btn-group">';
        echo $this->Html->link('<i class=" icon-circle-arrow-left"></i> Annuler', array('action' => 'traiter', $delib_id), array('class' => 'btn', 'name' => 'Annuler', 'escape' => false));
        echo $this->Form->button('<i class="icon-ok-sign"></i> Valider', array('div' => false, 'class' => 'btn btn-primary', 'name' => 'Valider', 'type' => 'submit'));
        echo '</div>';
        
    echo $this->Form->end();
?>

