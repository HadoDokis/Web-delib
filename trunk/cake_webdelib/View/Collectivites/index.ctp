<div class="seances">
<h2>Information de votre collectivit&eacute;</h2>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<th>Collectivité</th>
	<th>Logo</th>
	<th>Actions</th>
</tr>
<tr height='36px'>
	<td style="text-align:center"><?php echo $collectivite['Collectivite']['nom']; ?>
		<br/><br/><?php echo $collectivite['Collectivite']['adresse']; ?>
		<br/><?php echo $collectivite['Collectivite']['CP'].' '.$collectivite['0']['Collectivite']['ville']; ?>
		<br/><br/><?php echo $collectivite['Collectivite']['telephone']; ?>
	</td>
	<td><?php  echo $this->Html->image($logo_path);?></td>
	<td  class="actions">
		<?php echo $this->Html->link(SHY,'/collectivites/edit/1', array('class'=>'link_modifier', 'escape' => false,  'title'=>'Modifier'), false)?>

		<?php echo $this->Html->link(SHY,'/collectivites/setLogo/0', array('class'=>'link_inserer_logo', 'escape' => false,  'title'=>'Insérer Logo'), false)?>
	</td>

</tr>
</table>

</div>
