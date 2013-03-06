<h2>Envoyer le projet à un utilisateur</h2>

<?php
    $options = array('detour' =>'Envoyé (sans retour) : ', 'retour' => 'Aller-retour :', 'validation'=> 'Validation finale :');
    $attributes=array('legend'=>false, 'style'=>'float:right;', 'separator' => '<br/><br/>', 'value' => 'retour');

    echo $this->Form->create('Insert', array('url'=>'/deliberations/rebond/'.$delib_id,'type'=>'post'));
    echo $this->Form->input('user_id', array('label'=>'Destinataire', 'title'=>"A qui voulez vous envoyer le projet ? : "));
    echo '<br/>';
    if ($typeEtape == CAKEFLOW_COLLABORATIF) {
        $disable = 'disabled';
        echo $this->Form->hidden('retour', array('value'=>1));
        echo $this->Form->input('retour', array('label'=>'Aller-retour :', 'type'=>'radio' ,'disabled'=>'disabled', 'options' => $options));
        echo '<br/>';
        echo $this->Html->div('profil', 'Note : pour les étapes collaboratives (ET), l\'aller-retour est obligatoire.');
    } else {
        echo ('<div style="width: 200px">');
        echo $this->Form->radio('option', $options,$attributes);
        echo ('</div>');
    }
?>
<br/> <br/> <br/> <br />
<?php
        echo '<div class="submit">';
                echo $this->Form->submit('Valider', array('div'=>false, 'class'=>'bt_add', 'name'=>'Valider'));
                echo $this->Html->link('Annuler', array('action'=>'traiter', $delib_id), array('class'=>'link_annuler', 'name'=>'Annuler'));
        echo '</div>';
    echo $this->Form->end();
?>

