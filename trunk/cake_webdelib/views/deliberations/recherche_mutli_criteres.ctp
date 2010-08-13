<?php echo $javascript->link('calendrier.js'); ?>
<?php $afficheNote = false; ?>

<h2><?php echo $titreVue;?></h2>
<?php echo $form->create('Deliberation',array('type'=>'file','url'=>$action)); ?>

<div id="add_form">
<table class="sample">
    <tr>
            <td><?php echo $form->input('Deliberation.id', array('type'=>'text', 'between'=>'</td><td>','label'=>'Identifiant du projet','size' => '20'));?></td>
    </tr>
    <tr>
            <td><?php echo $form->input('Deliberation.rapporteur_id', array('between'=>'</td><td>','label'=>'Rapporteur', 'options'=>$rapporteurs, 'empty'=>true));?></td>
    </tr>
    <tr>
            <td><?php echo $form->input('Deliberation.seance1_id',array('between'=>'</td><td>','label'=>'Date s&eacute;ance entre ', 'options'=>$date_seances, 'empty'=>true));?></td>
    </tr>
    <tr>
            <td><?php echo $form->input('Deliberation.seance2_id',array('between'=>'</td><td>','label'=>' et ', 'options'=>$date_seances, 'empty'=>true));?></td>
    </tr>
    <tr>
            <td><?php echo $form->input('Deliberation.texte', array('between'=>'</td><td>','label'=>'Libell&eacute;','size' => '30'));?></td>
    </tr>
    <tr>
            <td><?php echo $form->input('Deliberation.service_id', array('between'=>'</td><td>','label'=>'Service Emetteur', 'options'=>$services, 'empty'=>true, 'escape'=>false));?></td>
    </tr>
    <tr>
            <td><?php echo $form->input('Deliberation.theme_id', array('between'=>'</td><td>','label'=>'Thème ', 'options'=>$themes, 'default'=>$html->value('Deliberation.theme_id'), 'empty'=>true));?></td>
    </tr>
    <tr>
            <td><?php echo $form->input('Deliberation.circuit_id', array('between'=>'</td><td>','label'=>'Circuit ', 'options'=>$circuits, 'default'=>$html->value('Deliberation.circuit_id'), 'empty'=>true));?></td>
    </tr>
    <tr>
            <td><?php echo $form->input('Deliberation.etat', array('between'=>'</td><td>','label'=>'Etat ', 'options'=> $etats, 'default'=>$html->value('Deliberation.etat'), 'empty'=>true));?></td>
    </tr>
	<?php foreach($infosupdefs as $infosupdef) {
		$fieldName = 'Infosup.'.$infosupdef['Infosupdef']['id'];
		echo '<tr>';
			echo '<td>'.$form->label($fieldName, $infosupdef['Infosupdef']['nom'].($infosupdef['Infosupdef']['type'] == 'date' ? '' : ' *')).'</td>';
			echo '<td>';
			if ($infosupdef['Infosupdef']['type'] == 'text' || $infosupdef['Infosupdef']['type'] == 'richText') {
				echo $form->input($fieldName, array('label'=>false, 'size'=>$infosupdef['Infosupdef']['taille'], 'title'=>$infosupdef['Infosupdef']['commentaire']));
				$afficheNote = true;
			} elseif ($infosupdef['Infosupdef']['type'] == 'date') {
				echo $form->input($fieldName, array('label'=>false, 'size'=>'9', 'title'=>$infosupdef['Infosupdef']['commentaire']));
				echo '&nbsp;';
				$fieldId = "'Deliberation.Infosup".Inflector::camelize($infosupdef['Infosupdef']['id'])."'";
				echo $html->link($html->image("calendar.png", "border='0'"), "javascript:show_calendar($fieldId, 'f');", array(), false, false);
			} elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
				echo $form->input($fieldName, array('label'=>false, 'options'=>$listeBoolean, 'empty'=>true));
			} elseif ($infosupdef['Infosupdef']['type'] == 'list') {
				echo $form->input($fieldName, array('label'=>false, 'options'=>$infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty'=>true));
			}
			echo '</td>';
		echo '</tr>';
	} ?>
</table>
<br />
<?php  echo $form->submit('Rechercher', array('div'=>false, 'class'=>'bt_add', 'name'=>'Rechercher')); ?>
</div>
<?php if ($afficheNote): ?>
<p>* : le caract&egrave;re % permet d'affiner les recherches comme indiqu&eacute; ci-dessous :
	<ul>
		<li>Commence par : texte% (si on recherche une information qui commence par 'Département' on écrit comme critère de recherche : Département%)</li>
		<li>Comprend : %texte% (si on recherche une information qui comprend 'avril' on écrit comme critère de recherche : %avril%)</li>
		<li>Finit par : %texte (si on recherche une information qui finit par 'clos.' on écrit comme critère de recherche : %clos.)</li>
	</ul>
</p>
<?php endif; ?>
