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
        $traitement_lot = false;
        $endDiv = true;
        echo $this->Html->tag('div', null, array('class'=>'ouvrable', 'id'=>$titreVue));
        echo $this->Html->tag('h2', "$titreVue $nb");
    }
    if (isset($traitement_lot) && ($traitement_lot ==true))
        echo $this->Form->create('Deliberation',array('url'=>'/deliberations/traitementLot','type'=>'post'));
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
		    <?php 
                        echo $this->Html->image($deliberation['iconeEtat']['image'], 
                                            array('alt'=>$deliberation['iconeEtat']['titre'].' '.$deliberation['Deliberation']['objet'],           
                                                  'title'=>$deliberation['iconeEtat']['titre'].' '.$deliberation['Deliberation']['objet']));
                        if (isset($traitement_lot) && ($traitement_lot ==true))
                            echo $this->Form->input('Deliberation.id_'.$deliberation['Deliberation']['id'], array('type' => 'checkbox', 'label' => false) );
                    ?> 
                            
		</td>
		<td>Service &eacute;metteur :<br/>
                <?php 
                       if (isset( $deliberation['Deliberation']['Service']['libelle']))
                           echo $deliberation['Deliberation']['Service']['libelle']; 
                       elseif (isset( $deliberation['Service']['libelle']))
                           echo $deliberation['Service']['libelle']; 
                ?></td>
		<td><?php echo $deliberation['Deliberation']['objet'];?></td>
		<td>S&eacute;ance(s) :<br />
		<?php
			if (in_array('attribuerSeance', $deliberation['Actions'])) {
				echo $this->Form->create('Deliberation',array('url'=>'/deliberations/attribuerSeance','type'=>'post'));
					echo $this->Form->input('Deliberation.seance_id',
                                                          array('type'     => 'select', 
                                                                'label'    => '', 
                                                                'options'  => $deliberation['Seances'],
                                                                'empty'    => false,
                                                                'multiple' => true));
					echo $this->Form->hidden('Deliberation.id',array('value'=> $deliberation['Deliberation']['id']));
					echo $this->Form->submit(' ', array('div'=>false, 'class'=>'bt_save', 'name'=>'sauvegarder'));
				echo $this->Form->end();
			} else {
                            if(isset($deliberation['Seance'][0])){
                                foreach( $deliberation['Seance'] as  $seance) {
                                    echo($typeseances[$seance['type_id']]." : ");
                                    echo($this->Html2->ukToFrenchDateWithHour($seance['date']).'<br>');
                                }
                            }
                        }
		?>
		</td>
		<td rowspan=3 class="actions">
                <br />
		<?php
                    if (in_array('view', $deliberation['Actions']))
			echo $this->Html->link(SHY, 
                                         '/deliberations/view/' . $deliberation['Deliberation']['id'], 
                                         array('class'=>'link_voir', 
						'alt' => 'Voir le projet '.$deliberation['Deliberation']['objet'],
						'title' => 'Voir le projet '.$deliberation['Deliberation']['objet'], 
                                                'escape' => false), 
                                                false);
                         
                    if (in_array('edit', $deliberation['Actions']) && ($deliberation['Deliberation']['signee'] != 1 ))
			echo $this->Html->link(SHY,
                                         '/deliberations/edit/' . $deliberation['Deliberation']['id'], 
                                         array('class'=>'link_modifier', 
					       'alt'=>'Modifier le projet '.$deliberation['Deliberation']['objet'],
                                               'title'=>'Modifier le projet '.$deliberation['Deliberation']['objet'],
                                               'escape' => false
                                               ), 
					      false);

                    if (in_array('delete', $deliberation['Actions']))
			echo $this->Html->link(SHY,
                                         '/deliberations/delete/'.$deliberation['Deliberation']['id'],  
					 array('class'=>'link_supprimer', 
                                               'alt'=>'Supprimer le projet '.$deliberation['Deliberation']['objet'], 
                                               'escape' => false,
                                               'title'=>'Supprimer le projet '.$deliberation['Deliberation']['objet'],
                                               'confirm'=>'Confirmez-vous la suppression du projet \''.$deliberation['Deliberation']['objet'].'\'?'), 
                                         'Etes-vous sur de vouloir supprimer le projet "' . $deliberation['Deliberation']['objet']. '" ?',
					 false);

                   if (in_array('traiter', $deliberation['Actions']))
                       echo $this->Html->link(SHY,
                                        "/deliberations/traiter/" . $deliberation['Deliberation']['id'], 
                                        array('class'=>"link_traiter", 
                                              'alt'=>'Traiter le projet '.$deliberation['Deliberation']['objet'],
                                              'escape' => false,
                                              'title'=>'Traiter le projet '.$deliberation['Deliberation']['objet']),
					      false);

                   if (in_array('validerEnUrgence', $deliberation['Actions']))
                       echo $this->Html->link(SHY,
                                        "/deliberations/validerEnUrgence/" . $deliberation['Deliberation']['id'], 
                                        array('class'=>"link_validerenurgence", 
					      'alt'=>'Valider en urgence le projet '.$deliberation['Deliberation']['objet'],
                                              'title'=>'Valider en urgence le projet '.$deliberation['Deliberation']['objet'],
                                              'escape' => false),
                                              'Confirmez-vous la validation en urgence du projet \''.$deliberation['Deliberation']['id'].'\'');
			
			 if (in_array('goNext', $deliberation['Actions']))
			     echo $this->Html->link(SHY,"/deliberations/goNext/" . $deliberation['Deliberation']['id'], 
                                                    array('class'=>"link_jump", 
							 'alt'=>'Sauter une ou des étapes pour le projet '.$deliberation['Deliberation']['objet'],
							 'title'=>'Sauter une ou des étapes pour le projet '.$deliberation['Deliberation']['objet'],
                                                         'escape' => false), 
                                                    false);
			
			echo '<br /><br/><br/><br/>';
			if (in_array('attribuerCircuit', $deliberation['Actions'])  && ($deliberation['Deliberation']['signee'] != 1 )) {
				$actionAttribuer = '/deliberations/attribuercircuit/' . $deliberation['Deliberation']['id'];
				$actionAttribuer .= $deliberation['Deliberation']['circuit_id'] ? '/'.$deliberation['Deliberation']['circuit_id'] : '';
				echo $this->Html->link(SHY, 
						 $actionAttribuer, 
                                                 array('class'=>'link_circuit', 
                                                       'alt'=>'Attribuer un circuit pour le projet '.$deliberation['Deliberation']['objet'],
                                                        'escape' => false,
                                                       'title'=>'Attribuer un circuit pour le projet '.$deliberation['Deliberation']['objet']), 
                                                       false);
                 
			}
			if (in_array('generer', $deliberation['Actions'])) {
                            if (empty($deliberation['Deliberation']['delib_pdf']))
		                echo $this->Html->link(SHY,
						       '/models/generer/' . $deliberation['Deliberation']['id'].'/null/'. $deliberation['Model']['id'], 
                                                      array('class'=>'link_pdf', 
	                                               'alt'=>'Visionner PDF pour le projet '.$deliberation['Deliberation']['objet'],
                                                       'escape' => false,
                                                       'title'=>'Visionner PDF pour le projet '.$deliberation['Deliberation']['objet'] ),
                                                 false);
			    else
			        echo $this->Html->link(SHY, 
                                                  '/deliberations/downloadDelib/'.$deliberation['Deliberation']['id'], 
			  			  array('class'=>'link_pdf', 
                                                        'alt'=>'Visionner PDF pour le projet '.$deliberation['Deliberation']['objet'],
                                                        'title'=>'Visionner PDF pour le projet '.$deliberation['Deliberation']['objet'],
                                                        'escape' => false), 
                                                       false);
			}
	?>
		</td>
	</tr>
	<tr>
		<td>
                    Circuit : <br /><?php echo $deliberation['Circuit']['nom']; ?>
                              <br /><?php if (isset( $deliberation['last_viseur']))
                          echo 'Visé par '. $deliberation['last_viseur'] ?>    
                </td>
		<td class='corps' rowspan=1 ><?php echo $deliberation['Deliberation']['titre']; ?></td>
		<td>A traiter avant le :<br /><?php echo $deliberation['Deliberation']['date_limite']; ?></td>
	</tr>
	<tr>
		<td>projet 
                <?php 
                       if (isset($deliberation['Typeacte']['libelle']))
                           $nature = $deliberation['Typeacte']['libelle'];
                   echo strtolower($nature) .' : '.$deliberation['Deliberation']['id']; ?>
                </td>
		<td class='corps' rowspan=1 >Th&egrave;me : 
                <?php 
                  if (isset( $deliberation['Theme']['libelle']))
                      echo $deliberation['Theme']['libelle'];
                ?></td>
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
	      echo '<li>'.$this->Html->link('Ajouter un projet', 
				      '/deliberations/add', 
                                      array('class'=>'link_add', 
                                            'escape' => false,
					    'alt'=>'créer un nouveau projet',
                                            'title'=>'créer un nouveau projet')).'</li>';
	      echo '</ul>';
              
          }
	  if (in_array('mesProjetsRecherche', $listeLiens)) {
              echo '<ul class="actions">';
              echo '<li>'.$this->Html->link('Nouvelle recherche', '/deliberations/mesProjetsRecherche', array('class'=>'link_add', 'escape' => false, 'alt'=>'Nouvelle recherche parmi mes projets', 'title'=>'Nouvelle recherche parmi mes projets')).'</li>'; 
              echo '</ul>';
          }
	  if (in_array('tousLesProjetsRecherche', $listeLiens)) {
              echo '<ul class="actions">';
              echo '<li>'.$this->Html->link('Nouvelle recherche', '/deliberations/tousLesProjetsRecherche', array('class'=>'link_add', 'escape' => false, 'alt'=>'Nouvelle recherche parmi tous les projets', 'title'=>'Nouvelle recherche parmi tous les projets')).'</li>';
              echo '</ul>';
          }
} ?>

</div>
<br />
<?php 
     if (isset($traitement_lot) && ($traitement_lot ==true)) {
         echo $this->Form->input('Deliberation.action', array('options' => $actions_possibles, 
                                                              'div'     => false,
                                                              'empty'   => 'Selectionner une action'));
         echo $this->Form->Submit('Executer', array('div' => false, 
                                                    'before' => '&nbsp;&nbsp;&nbsp;'));
         echo $this->Form->end(); 
     }
     if ($endDiv)
         echo ('</div>');
?>
