<div class="users">
<h2>Liste des utilisateurs</h2>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<th><?php echo $paginator->sort('Login', 'login'); ?></th>
	<th><?php echo $paginator->sort('Nom', 'nom'); ?></th>
	<th><?php echo $paginator->sort('Pr�nom', 'prenom'); ?></th>
	<th><?php echo $paginator->sort('Profil', 'Profil.libelle'); ?></th>
	<th>T�l�phone</th>
	<th>Mobile</th>
	<th><?php echo $paginator->sort('Services', 'Service.libelle'); ?></th>
	<th>Natures</th>
	<th width='20%'>Actions</th>
</tr>
<?php

foreach ($users as $user):?>
<tr height="36px">
	<td><?php echo $user['User']['login']; ?></td>
	<td><?php echo $user['User']['nom']; ?></td>
	<td><?php echo $user['User']['prenom']; ?></td>
	<td><?php echo $user['Profil']['libelle']; ?></td>
	<td><?php echo $user['User']['telfixe']; ?></td>
	<td><?php echo $user['User']['telmobile']; ?></td>
	<td><?php
		foreach ($user['Service'] as $service):
			echo $service['libelle'].'<br/>';
		endforeach;
	?></td>
       <td><?php
                foreach ($user['Natures'] as $nature){
                    if($nature['Nature']['check'])
                        echo($nature['Nature']['libelle']).'<br/>';
                }
        ?></td>


	<td class="actions">
		<?php echo $html->link(SHY,'/users/view/' . $user['User']['id'], array('class'=>'link_voir', 'title'=>'Voir'), false, false)?>
		<?php echo $html->link(SHY,'/users/edit/' . $user['User']['id'], array('class'=>'link_modifier', 'title'=>'Modifier'), false, false)?>
		<?php echo $html->link(SHY,'/users/changeMdp/' . $user['User']['id'], array('class'=>'link_mdp', 'title'=>'Nouveau mot de passe'), false, false)?>

		<?php
		    if ($Users->_isDeletable($user, $message))
		        echo $html->link(SHY,'/users/delete/' . $user['User']['id'], array('class'=>'link_supprimer', 'title'=>'Supprimer'), 'Etes-vous sur de vouloir supprimer l\'utilisateur "' . $user['User']['prenom'] . ' ' . $user['User']['nom'] .'" ?', false)?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<div class='paginate'>
	<!-- Affiche les num�ros de pages -->
	<?php echo $paginator->numbers(); ?>
	<!-- Affiche les liens des pages pr�c�dentes et suivantes -->
	<?php
		echo $paginator->prev('� Pr�c�dent ', null, null, array( 'tag' => 'span', 'class' => 'disabled'));
		echo $paginator->next(' Suivant �', null, null, array( 'tag' => 'span', 'class' => 'disabled'));
	?> 
	<!-- Affiche X de Y, o� X est la page courante et Y le nombre de pages -->
	<?php echo $paginator->counter(array('format'=>'Page %page% sur %pages%')); ?>
</div>

<ul class="actions">
	<li><?php echo $html->link('Ajouter', '/users/add/', array('class'=>'link_add', 'title'=>'Ajouter un utilisateur')); ?></li>
</ul>
</div>

