<?php
echo $this->Bs->tag('h3', 'Compteur');
?>
<div class="panel panel-default">
    <div class="panel-heading">Fiche Compteur: <?php echo $compteur['Compteur']['nom'] ?></div>
    <div class="panel-body">
    <dl>

<div class="imbrique">
	<div class="gauche">
		<dt>Nom</dt>
		<dd>&nbsp;<?php echo $compteur['Compteur']['nom']?></dd>
	</div>
	<div class="droite">
		<dt>Commentaire</dt>
		<dd>&nbsp;<?php echo $compteur['Compteur']['commentaire']?></dd>
	</div>
</div>

<div class="imbrique">
	<div class="gauche">
		<dt>D&eacute;finition du compteur</dt>
		<dd>&nbsp;<?php echo $compteur['Compteur']['def_compteur']?></dd>
	</div>
	<div class="droite">
		<dt>Nom et num&eacute;ro de la s&eacute;quence</dt>
		<dd>&nbsp;<?php echo $compteur['Sequence']['nom'].' : '.$compteur['Sequence']['num_sequence']?></dd>
	</div>
</div>

<div class="imbrique">
	<div class="gauche">
		<dt>Crit&egrave;re de r&eacute;initialisation de la s&eacute;quence</dt>
		<dd>&nbsp;<?php echo $compteur['Compteur']['def_reinit']?></dd>
	</div>
	<div class="droite">
		<dt>Dernière valeur calcul&eacute;e du crit&egrave;re de r&eacute;initialisation</dt>
		<dd>&nbsp;<?php echo $compteur['Compteur']['val_reinit']?></dd>
	</div>
</div>

<div class="imbrique">
	<div class="gauche">
		<dt>Date de cr&eacute;ation</dt>
		<dd>&nbsp;<?php echo $compteur['Compteur']['created']?></dd>
	</div>
	<div class="droite">
		<dt>Date de modification</dt>
		<dd>&nbsp;<?php echo $compteur['Compteur']['modified']?></dd>
	</div>
</div>
</dl>        </ul>

    <br/>
<?php
echo $this->Bs->row().
$this->Bs->col('md4 of5');
echo $this->Bs->div('btn-group', null,array('id'=>"actions_fiche" )) .
    $this->Html2->btnCancel().
    $this->Bs->btn('Modifier', array('controller' => 'compteurs', 'action' => 'edit', $compteur['Compteur']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
    $this->Bs->close(6);