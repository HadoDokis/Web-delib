<div class="deliberations">

<?php 

    if ($nbProjets > 1) 
        $nb = "($nbProjets projets)";
    else
	$nb = "($nbProjets projet)";

    if ((@$this->params['filtre'] != 'hide' ) &&
        ($this->params['action'] !='mesProjetsRecherche') && 
        ($this->params['action'] !='tousLesProjetsRecherche') ) {
        echo $this->element('filtre'); 
        echo "<h2>$titreVue $nb</h2>";
        $endDiv = false;
    }
    else {
        $endDiv = true;
	echo $html->tag('div', null, array('class'=>'ouvrable', 'id'=>$titreVue));
        echo $html->tag('h2', "$titreVue $nb");
    }

?>
	<table width="100%" cellspacing="0" cellpadding="0" caption="<?php echo $titreVue;?>" summary="<?php echo $titreVue;?>" >
	<tr>
		<th width='5%' align="right">Vue </th>
		<th width='15%' align="left">synth&eacute;tique</th>
		<th width='46%'> &nbsp;</th>
		<th width='18%' >&nbsp;</th>
		<th width='250px'>Actions</th>
	</tr>
	<tr>
		<td colspan='5' class='Border' height='1' >
		</td>
	</tr>

<?php foreach ($this->data as $deliberation){ ?>
	<tr>
		<td rowspan=3 style="text-align:center;">
		    <br />
		    <?php echo $html->image($deliberation['iconeEtat']['image'], 
                                            array('alt'=>$deliberation['iconeEtat']['titre'].' '.$deliberation['Deliberation']['objet'],
                                                  'title'=>$deliberation['iconeEtat']['titre'].' '.$deliberation['Deliberation']['objet']));?>
		</td>
		<td>Service &eacute;metteur :<br/><?php echo $deliberation['Service']['libelle'];?></td>
		<td><?php echo $deliberation['Deliberation']['objet'];?></td>
		<td>S&eacute;ance :<br />
		<?php
			if (in_array('attribuerSeance', $deliberation['Actions'])) {
				echo $form->create('Deliberation',array('url'=>'/deliberations/attribuerSeance','type'=>'post'));
					echo $form->input('Deliberation.seance_id',array('type'=>'select', 'label'=>'', 'options'=>$date_seances,'empty'=>true));
					echo $form->hidden('Deliberation.id',array('value'=> $deliberation['Deliberation']['id']));
					echo $form->submit(' ', array('div'=>false, 'class'=>'bt_save', 'name'=>'sauvegarder'));
				echo $form->end();
			} else {
                            if($deliberation['Seance']['date'] != ''){
                                echo $deliberation['Seance']['libelle'].'<br />';
                                echo $deliberation['Seance']['date'];
                            }
                        }
		?>
		</td>
		<td rowspan=3 class="actions">
                <br />
		<?php
                    if (in_array('view', $deliberation['Actions']))
			echo $html->link(SHY, 
                                         '/deliberations/view/' . $deliberation['Deliberation']['id'], 
                                         array('class'=>'link_voir', 
						'alt' => 'Voir le projet '.$deliberation['Deliberation']['objet'],
						'title' => 'Voir le projet '.$deliberation['Deliberation']['objet']), 
						false, 
                                                false);
                         
                    if (in_array('edit', $deliberation['Actions']) && ($deliberation['Deliberation']['signee'] != 1 ))
			echo $html->link(SHY,
                                         '/deliberations/edit/' . $deliberation['Deliberation']['id'], 
                                         array('class'=>'link_modifier', 
					       'alt'=>'Modifier le projet '.$deliberation['Deliberation']['objet'],
                                               'title'=>'Modifier le projet '.$deliberation['Deliberation']['objet']
                                               ), 
					      false, 
					      false);

                    if (in_array('delete', $deliberation['Actions']))
			echo $html->link(SHY,
                                         '/deliberations/delete/'.$deliberation['Deliberation']['id'],  
					 array('class'=>'link_supprimer', 
                                         'alt'=>'Supprimer le projet '.$deliberation['Deliberation']['objet'], 
                                         'title'=>'Supprimer le projet '.$deliberation['Deliberation']['objet']), 
                                         'Etes-vous sur de vouloir supprimer le projet "' . $deliberation['Deliberation']['objet']. '" ?', 
					 false);

                   if (in_array('traiter', $deliberation['Actions']))
                       echo $html->link(SHY,
                                        "/deliberations/traiter/" . $deliberation['Deliberation']['id'], 
                                        array('class'=>"link_traiter", 
                                              'alt'=>'Traiter le projet '.$deliberation['Deliberation']['objet'],
                                              'title'=>'Traiter le projet '.$deliberation['Deliberation']['objet']),
                                              false, 
					      false);

                   if (in_array('validerEnUrgence', $deliberation['Actions']))
                       echo $html->link(SHY,
                                        "/deliberations/validerEnUrgence/" . $deliberation['Deliberation']['id'], 
                                        array('class'=>"link_validerenurgence", 
					      'alt'=>'Valider en urgence le projet '.$deliberation['Deliberation']['objet'],
                                              'title'=>'Valider en urgence le projet '.$deliberation['Deliberation']['objet']), 
                                              'Confirmez-vous la validation en urgence du projet \''.$deliberation['Deliberation']['id'].'\'', false);
			
			 if (in_array('goNext', $deliberation['Actions']))
			     echo $html->link(SHY,"/deliberations/goNext/" . $deliberation['Deliberation']['id'], 
                                                   array('class'=>"link_jump", 
							 'alt'=>'Sauter une ou des �tapes pour le projet '.$deliberation['Deliberation']['objet'],
							 'title'=>'Sauter une ou des �tapes pour le projet '.$deliberation['Deliberation']['objet'],
                     
                                                          ), false, false);
			
			echo '<br /><br/><br/><br/>';
			if (in_array('attribuerCircuit', $deliberation['Actions'])  && ($deliberation['Deliberation']['signee'] != 1 )) {
				$actionAttribuer = '/deliberations/attribuercircuit/' . $deliberation['Deliberation']['id'];
				$actionAttribuer .= $deliberation['Deliberation']['circuit_id'] ? '/'.$deliberation['Deliberation']['circuit_id'] : '';
				echo $html->link(SHY, 
						 $actionAttribuer, 
                                                 array('class'=>'link_circuit', 
                                                       'alt'=>'Attribuer un circuit pour le projet '.$deliberation['Deliberation']['objet'],
                                                       'title'=>'Attribuer un circuit pour le projet '.$deliberation['Deliberation']['objet']), false, false);
			}
			if (in_array('generer', $deliberation['Actions'])) {
                            if ($deliberation['Seance']['traitee']==0)
		                echo $html->link(SHY,
						'/models/generer/' . $deliberation['Deliberation']['id'].'/null/'. $deliberation['Model']['id'], array('class'=>'link_pdf', 
	  'alt'=>'Visionner PDF pour le projet '.$deliberation['Deliberation']['objet'],
          'title'=>'Visionner PDF pour le projet '.$deliberation['Deliberation']['objet'] ),
           false, 
           false);
			     else
				 echo $html->link(SHY, 
                                                  '/deliberations/downloadDelib/'.$deliberation['Deliberation']['id'], 
						  array('class'=>'link_pdf', 
                                                        'alt'=>'Visionner PDF pour le projet '.$deliberation['Deliberation']['objet'],
                                                        'title'=>'Visionner PDF pour le projet '.$deliberation['Deliberation']['objet']), 
                                                       false, 
                                                       false);
			}
		?>
		</td>
	</tr>
	<tr>
		<td>
                    Circuit : <br /><?php echo $deliberation['Circuit']['libelle']; ?>
                              <br /><?php if (isset( $deliberation['last_viseur']))
                          echo 'Vis� par '. $deliberation['last_viseur'] ?>    
                </td>
		<td class='corps' rowspan=1 ><?php echo $deliberation['Deliberation']['titre']; ?></td>
		<td>A traiter avant le :<br /><?php echo $deliberation['Deliberation']['date_limite']; ?></td>
	</tr>
	<tr>
		<td>projet <?php echo strtolower($deliberation['Nature']['libelle']) .' : '.$deliberation['Deliberation']['id']; ?></td>
		<td class='corps' rowspan=1 >Th&egrave;me : 
                <?php echo $deliberation['Theme']['libelle'] ?></td>
		<td>Classification : <?php echo $deliberation['Deliberation']['num_pref'];  ?></td>
	</tr>
	<tr>
		<td colspan='5' class='Border' height='1' >
		</td>
	</tr>
<?php } ?>

</table>

<?php if (!empty($listeLiens)) {
	  if (in_array('add', $listeLiens)) {
              echo '<ul class="actions">';
	      echo '<li>'.$html->link('Ajouter un projet', 
				      '/deliberations/add', 
                                      array('class'=>'link_add', 
					    'alt'=>'cr�er un nouveau projet',
                                            'title'=>'cr�er un nouveau projet')).'</li>';
	      echo '</ul>';
              
          }
	  if (in_array('mesProjetsRecherche', $listeLiens)) {
              echo '<ul class="actions">';
              echo '<li>'.$html->link('Nouvelle recherche', '/deliberations/mesProjetsRecherche', array('class'=>'link_add', 'alt'=>'Nouvelle recherche parmi mes projets', 'title'=>'Nouvelle recherche parmi mes projets')).'</li>'; 
              echo '</ul>';
          }
	  if (in_array('tousLesProjetsRecherche', $listeLiens)) {
              echo '<ul class="actions">';
              echo '<li>'.$html->link('Nouvelle recherche', '/deliberations/tousLesProjetsRecherche', array('class'=>'link_add', 'alt'=>'Nouvelle recherche parmi tous les projets', 'title'=>'Nouvelle recherche parmi tous les projets')).'</li>';
              echo '</ul>';
          }
} ?>

</div>
<?php 
     $form->end(); 
     if ($endDiv)
         echo ('</div>');
?>
