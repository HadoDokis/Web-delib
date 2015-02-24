<?php
echo $this->Bs->tag('h3', 'Séquence');
?>
<div class="panel panel-default">
    <div class="panel-heading">Fiche S&eacute;quence: <?php echo $sequence['Sequence']['nom'] ?></div>
    <div class="panel-body">
<div class="imbrique">
	<div class="gauche">
		<dt>Libelle</dt>
		<dd>&nbsp;<?php echo $sequence['Sequence']['nom']?></dd>
	</div>
	<div class="droite">
		<dt>Commentaire</dt>
		<dd>&nbsp;<?php echo $sequence['Sequence']['commentaire']?></dd>
	</div>
</div>

<div class="imbrique">
	<div class="gauche">
		<dt>Num&eacute;ro de la s&eacute;quence</dt>
		<dd>&nbsp;<?php echo $sequence['Sequence']['num_sequence']?></dd>
	</div>
</div>

<div class="imbrique">
	<div class="gauche">
		<dt>Date de cr&eacute;ation</dt>
		<dd>&nbsp;<?php echo $sequence['Sequence']['created']?></dd>
	</div>
	<div class="droite">
		<dt>Date de modification</dt>
		<dd>&nbsp;<?php echo $sequence['Sequence']['modified']?></dd>
	</div>
</div>

</dl>        </ul>

    <br/>
<?php
echo $this->Bs->row().
$this->Bs->col('md4 of5');
echo $this->Bs->div('btn-group', null,array('id'=>"actions_fiche" )) .
    $this->Html2->btnCancel(),
    $this->Bs->btn('Modifier', array('controller' => 'sequences', 'action' => 'edit', $sequence['Sequence']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
    $this->Bs->close(6);