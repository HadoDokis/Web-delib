<div class="commentaire">
<h2>Fiche commentaire</h2>

<dl>

	<dt>Delib Id</dt>
	<dd>&nbsp;<?php echo $commentaire['Commentaire']['delib_id']?></dd>
	<dt>Agent</dt>
	<dd>&nbsp;<?php echo $commentaire['Commentaire']['agent_id']?></dd>
	<dt>Texte</dt>
	<dd>&nbsp;<?php echo $commentaire['Commentaire']['texte']?></dd>
	<dt>Created</dt>
	<dd>&nbsp;<?php echo $commentaire['Commentaire']['created']?></dd>

</dl>
<ul class="actions">
	<li><?php echo $this->Html->link('Retour delib',   '/deliberations/traiter/' . $commentaire['Commentaire']['delib_id']) ?> </li>

</ul>

</div>