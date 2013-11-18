<div class="typeseances">
<h2>Modèles d'édition</h2>
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<th>Mod&egrave;le</th>
	<th>Type</th>
	<th>Nom du mod&egrave;le</th>
	<th width='25px'>Recherche</th>
	<th width='25px'>Joindre les annexes</th>
	<th width='25px'>Multi-séances</th>
	<th>Dernière modification</th>
	<th>Actions</th>
</tr>
<?php foreach ($models as $model): ?>
<tr height="36px">
	<td><?php echo $model['Model']['modele']; ?></td>
	<td><?php echo $model['Model']['type']; ?></td>
	<td><?php echo $model['Model']['name']; ?></td>
	<td  style="text-align: center;"><?php  
             if ($model['Model']['recherche'] == '') $model['Model']['recherche'] = 0;
             echo $this->Html->link(SHY, 
                              '/models/changeStatus/recherche/'.$model['Model']['id'], 
                              array('class'=>'link_bool_'.$model['Model']['recherche'], 
                            	    'escape' => false, 
                                    'title'=>'Inclure dans la recherche'),
                              false);
         ?></td>
         <td style="text-align: center;"><?php
             if ($model['Model']['joindre_annexe'] == '') $model['Model']['joindre_annexe'] = 0;
             echo $this->Html->link(SHY, 
                              '/models/changeStatus/joindre_annexe/'.$model['Model']['id'], 
                              array('class'=>'link_bool_'.$model['Model']['joindre_annexe'], 
                       		       'escape' => false, 
                                    'title'=>'Ajouter les annexes en fin de document (export PDF)'),
                              false);
         ?></td>
           <td style="text-align: center;"><?php
             if ($model['Model']['multiodj'] == '') $model['Model']['multiodj'] = 0;
             echo $this->Html->link(SHY,
                              '/models/changeStatus/multiodj/'.$model['Model']['id'], 
                              array('class'=>'link_bool_'.$model['Model']['multiodj'], 
                                    'escape' => false, 
                                    'title'=>'Multi-séances'),
                              false);
         ?></td>


	<td><?php if ($model['Model']['modified']!= 0)
                   echo $model['Model']['modified']; 
         ?></td>
	<td class="actions">
            <?php echo $this->Html->link(SHY,'/models/view/' . $model['Model']['id'], array('class'=>'link_voir', 'escape' => false,  'title'=>'Voir'), false)?>
	    <?php echo $this->Html->link(SHY,'/models/import/' . $model['Model']['id'], array('class'=>'link_modifier', 'escape' => false, 'title'=>'Modifier'), false); ?>
	    <?php if ( ($model['Model']['type'] == 'Document') && ($model['Model']['id'] != 1) && ($deletable[$model['Model']['id']]))
                    echo $this->Html->link(SHY,'/models/delete/' . $model['Model']['id'], array('class'=>'link_supprimer', 'escape' => false,  'title'=>'Supprimer'), "Confirmer la suppression du modèle d'édition ?");
            ?>
	</td>
</tr>
<?php endforeach; ?>
</table>
<!--    <ul class="actions">
	<li><?php echo $this->Html->link('Ajouter', '/models/add/', array('class'=>'link_add', 'title'=>'Ajouter un modele')); ?></li>
    </ul>-->

<?php $this->Html2->boutonAdd("Ajouter","Ajouter un modèle"); ?>
</div>
