<h2>Envoyer le projet à un utilisateur</h2>

<?php
    echo $form->create('Insert', array('url'=>'/deliberations/rebond/'.$delib_id,'type'=>'post'));
    echo $form->input('user_id', array('label'=>'Destinataire', 'title'=>"A qui voulez vous envoyer le projet ? : "));
    echo '<br/>';
	echo $form->input('retour', array('label'=>'Aller-retour :', 'type'=>'checkbox'));
?>
<br/><br/>
<?php
	echo '<div class="submit">';
		echo $form->submit('Valider', array('div'=>false, 'class'=>'bt_add', 'name'=>'Valider'));
		echo $html->link('Annuler', array('action'=>'traiter', $delib_id), array('class'=>'link_annuler', 'name'=>'Annuler'));
	echo '</div>';
    echo $form->end();
?>
