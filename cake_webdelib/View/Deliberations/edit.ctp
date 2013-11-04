<?php echo $this->Html->script('calendrier.js'); ?>
<?php echo $this->Html->script('utils.js'); ?>
<?php echo $this->Html->script('deliberation.js'); ?>
<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Html->script('ckeditor/adapters/jquery'); ?>
<?php echo $this->Html->script('multidelib.js'); ?>

<script>
function reset_html(id) {
    $('#'+id).html($('#'+id).html());
}
$(document).ready(function() {

    var file_input_index = 0;
    
    $('.file-texte').each(function() {
        file_input_index++;
        $(this).wrap('<div id="file_input_container_'+file_input_index+'"></div>');
        $(this).after('<input type="button" value="Effacer" class="purge_file"  onclick="javascript: reset_html(\'file_input_container_'+file_input_index+'\')" />');
    });
   
});
</script>


<?php
echo "<h1>Modification du projet : ".$this->Html->value('Deliberation.id')."</h1>";
echo $this->Form->create('Deliberation', array('url'=>'/deliberations/edit/'.$this->Html->value('Deliberation.id'), 'type'=>'file', 'name'=>'Deliberation'));
?>

<div class='onglet'>
	<a href="#" id="emptylink" alt=""></a>
        <a href="javascript:afficheOnglet(1)" id='lienTab1' <?php echo !isset($lienTab) || (isset($lienTab) && ($lienTab==1 || empty($lienTab)))?'class="ongletCourant"':''?>>Informations principales</a>
	<a href="javascript:afficheOnglet(2)" id='lienTab2' <?php echo isset($lienTab) &&  $lienTab==2?'class="ongletCourant"':''?>>Textes</a>
	<a href="javascript:afficheOnglet(3)" id='lienTab3' <?php echo isset($lienTab) &&  $lienTab==3?'class="ongletCourant"':''?>>Annexe(s)</a>
<?php if (!empty($infosupdefs)): ?>
	<a href="javascript:afficheOnglet(4)" id='lienTab4' <?php echo isset($lienTab) &&  $lienTab==4?'class="ongletCourant"':''?>>Informations suppl&eacute;mentaires</a>
<?php endif; ?>
<?php if (Configure::read('DELIBERATIONS_MULTIPLES')): ?>
	<a href="javascript:afficheOnglet(5)" id='lienTab5' style="display: none" <?php echo isset($lienTab) && $lienTab==5?'class="ongletCourant"':''?>>D&eacute;lib&eacute;rations rattach&eacute;es</a>
<?php endif; ?>
</div>

<div id="tab1"  <?php echo isset($lienTab) && $lienTab!=1?'style="display:none"':''?>>
        <fieldset id='info'>
	<div class='demi'>
		<?php echo '<b><u>Rédacteur</u></b> : <i>'.$this->Html->value('Redacteur.prenom').' '.$this->Html->value('Redacteur.nom').'</i>';?>
		<br/>
		<?php echo '<b><u>Service émetteur</u></b> : <i>'.$this->Html->value('Service.libelle').'</i>'; ?>
	</div>
	<div class='demi'>
		<?php echo '<b><u>Date cr&eacute;ation</u></b> : <i>'. $this->Html->value('Deliberation.created').'</i>';?>
		<br/>
		<?php echo '<b><u>Date de modification</u></b> : <i>'. $this->Html->value('Deliberation.modified').'</i>';?>
	</div>
        </fieldset>
	<div class='spacer'></div>
        <?php echo $this->Form->input('Deliberation.typeacte_id', array('label'    => 'Type d\'acte <abbr title="obligatoire">(*)</abbr>',
                                                                        'options'  => $this->Session->read('user.Nature'), 
                                                                        'empty'    => false, 
                                                                        'id'       => 'listeTypeactesId',
                                                                        'onChange' => "updateTypeseances(this);", 
                                                                        'escape'   => false));  ?>
	<div class='spacer'></div>
 	<?php echo $this->Form->input('Deliberation.objet', array('type'=>'textarea','label'=>'Libellé <abbr title="obligatoire">(*)</abbr>','cols' => '60','rows'=> '2'));?>

	<div class='spacer'></div>
 	<?php echo $this->Form->input('Deliberation.titre', array('type'=>'textarea','label'=>'Titre','cols' => '60','rows'=> '2'));?>

	<div class='spacer'></div>

        <div id='selectTypeseances' class='gauche'>
        <?php
          if (!empty( $typeseances))
             echo $this->Form->input('Typeseance', array('options'  => $typeseances,
                    'type'     =>'select',
                    'label'    => 'Types de séance',
                    'autocomplete' => 'off',
                    'size'     => 10,
                    'onchange' => "updateDatesSeances(this);",
                    'multiple' => true,
                    'selected' => isset($typeseances_selected)?$typeseances_selected:''));
        ?>
        </div>
        <div id='selectDatesSeances' class='droite'>
            
        <?php
      /* debug($seances);
        debug($seances_selected);*/
          if (!empty($seances))
                echo $this->Form->input('Seance', array( 'options'  => $seances,
                                                        'type'=>'select',
                                                         'label'    => 'Dates de séance',
                                                        'autocomplete' => 'off',
                                                         'size'     => 10,
                                                         'multiple' => true,
                                                        'selected' =>isset($seances_selected)?$seances_selected:''));
        ?>  
        </div>

	<div class='spacer'></div>
	<?php echo $this->Form->input('Deliberation.rapporteur_id', array('label'=>'Rapporteur', 'options'=>$rapporteurs, 'empty'=>true)); ?>

	<div class='spacer'></div>
	<?php echo $this->Form->input('Deliberation.theme_id', array('label'=>'Thème <abbr title="obligatoire">(*)</abbr>', 'options'=>$themes, 'default'=>$this->Html->value('Deliberation.theme_id'), 'empty'=>false, 'escape'=>false)); ?>
	<div class='spacer'></div>

	<?php 
        if ($USE_PASTELL){
		if (empty($nomenclatures)) $nomenclatures = array();
                echo $this->Form->input('Deliberation.num_pref_libelle', array('label'=>'Nomenclature', 'options'=>$nomenclatures, 'default'=>$this->Html->value('Deliberation.num_pref'), 'disabled' =>  empty($nomenclatures), 'empty' => "Aucune", 'escape'=>false)); 
        }else {
                echo $this->Form->input( 'Deliberation.num_pref_libelle',
				   array('div'      => false,
                                         'label'    => 'Num Pref',
                                         'id'       => 'classif1', 
                                         'size'     => '60',
					 'readonly' => 'readonly'));
        ?>

                <a class="list_form" href="#add" onclick="javascript:window.open('<?php echo $this->base; ?>/deliberations/classification', 'Select_attribut', 'scrollbars=yes,width=570,height=450');" id="classification_text">[Choisir la classification]</a>
        <?php 
               echo $this->Form->hidden('Deliberation.num_pref',array('id'=>'num_pref'));
        }
        ?>
	<div class='spacer'></div>

	<?php echo $this->Form->label('Deliberation.date_limite', 'Date limite');?>
	<?php
		if (!empty($this->data['Deliberation']['date_limite']) && $this->data['Deliberation']['date_limite'] != '01/01/1970')
			$value = "value='".$this->data['Deliberation']['date_limite']."'";
		else
			$value = "value=''";
	?>
	<input name="date_limite" size="9" <?php echo $value; ?> />&nbsp;<a href="javascript:show_calendar('Deliberation.date_limite','f');" alt="" id="afficheCalendrier"><?php echo $this->Html->image("calendar.png", array('style'=>"border='0'")); ?></a>
	<div class='spacer'></div>


<?php 
        if ($DELIBERATIONS_MULTIPLES) {
           echo $this->Form->input('Deliberation.is_multidelib', array(
	                     'type'=>'checkbox',
                              'autocomplete'=>'off',
	                     'disabled'=>  isset($this->data['Multidelib']) ,
                             'checked'=>  isset($this->data['Multidelib']) OR (isset($this->data['Deliberation']['is_multidelib']) && $this->data['Deliberation']['is_multidelib']==1)?true:false,
		             'label'=>'Multi Délibération',
                               'onClick'=>'multiDelib(this)'));
        }
?>

	<div class='spacer'></div>
</div>

<div id="tab2" <?php echo isset($lienTab) && $lienTab==2?'':'style="display: none;"'?>>
    <?php echo $this->element('texte', array('key' => 'texte_projet'));?>
	<div class='spacer'></div>

    <?php echo $this->element('texte', array('key' => 'texte_synthese'));?>
	<div class='spacer'></div>

	<div id='texteDelibOngletTextes'>
            <div id='texteDeliberation'>
                <?php echo $this->element('texte', array('key' => 'deliberation'));?>
            </div>
        </div>
        <?php 
        echo $this->Html->tag('div', '', array('class'=>'spacer'));
        echo $this->Html->tag('p', 'Note : les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.');
        ?>
	<div class='spacer'></div>
</div>

<div id="tab3" <?php echo isset($lienTab) && $lienTab==3?'':'style="display: none;"'?>>
    <div id='DelibOngleAnnexes'>
    <div id="DelibPrincipaleAnnexes">
	<?php
	$annexeOptions = array('ref' => 'delibPrincipale');
	$tabAnnexes = array();
        if (isset($this->data['Annex'])) {
	    foreach ($this->data['Annex'] as $annexe) {
	        if (isset($annexe['model']) && $annexe['model'] == 'Projet') 
		    $tabAnnexes[] = $annexe;
            }
        }
	if (isset($this->data['Annex'])) $annexeOptions['annexes'] = $tabAnnexes;
            echo $this->element('annexe', $annexeOptions);
        ?>
        </div></div>
        <?php
	echo $this->Html->tag('div', '', array('class'=>'spacer'));
	echo $this->Html->tag('p', 'Note : les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.');
        ?>
        
</div>

<?php if (!empty($infosupdefs)): ?>
<div id="tab4" <?php echo isset($lienTab) && $lienTab==4 ? '' : 'style="display: none;"' ?>>
	<?php
	foreach($infosupdefs as $infosupdef) {
		// Amélioration 4.1 : on ne peut modifier une infosup qu'en fonction du profil
		$disabled = true;
		foreach ($infosupdef['Profil'] as $profil) 
			if ($profil['id'] == $profil_id) 
                            $disabled = false;
                        
                if ($infosupdef['Infosupdef']['type'] == 'file' && $disabled) continue;
                
		$fieldName = 'Infosup.'.$infosupdef['Infosupdef']['code'];
		$fieldId = 'Infosup'.Inflector::camelize($infosupdef['Infosupdef']['code']);
		echo "<div class='required'>";
			echo $this->Form->label($fieldName, $infosupdef['Infosupdef']['nom'], array('name'=>'label'.$infosupdef['Infosupdef']['code']));
			if ($infosupdef['Infosupdef']['type'] == 'text') {
				echo $this->Form->input($fieldName, array('label' => false, 'type'=> 'textarea', 'title'=>$infosupdef['Infosupdef']['commentaire'], 'readonly'=> $disabled  ));
			} elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
                            if (!$disabled)
				echo $this->Form->input($fieldName, array('label' => false, 'type'=>'checkbox', 'title'=>$infosupdef['Infosupdef']['commentaire'], 'div'=>array('class'=>'input')));
                            else{
                                echo $this->Form->input($fieldName, array('label' => false, 'type'=>'checkbox', 'title'=>$infosupdef['Infosupdef']['commentaire'], 'disabled'=>$disabled, 'div'=>array('class'=>'input')));
                                echo $this->Form->input($fieldName, array('type'=>'hidden', 'id'=>false));
                            }
                        } elseif ($infosupdef['Infosupdef']['type'] == 'date') {
				echo $this->Form->input($fieldName, array('type'=>'text',  'readonly'=> $disabled,  'div' => false, 'label' => false, 'size'=>'9', 'title'=>$infosupdef['Infosupdef']['commentaire']));
				echo '&nbsp;';
				if (!$disabled)
					echo $this->Html->link($this->Html->image("calendar.png", array('style'=>"border='0'")), "javascript:show_calendar('Deliberation.$fieldId', 'f');", array('escape' =>false), false); 
				else
					echo($this->Html->image("calendar.png", array('style'=>"border='0'")));
			} elseif ($infosupdef['Infosupdef']['type'] == 'richText') {
                                    echo '<div class="annexesGauche"></div>';
                                if (!$disabled){
                                    echo '<div class="fckEditorProjet">';
                                            echo $this->Form->input($fieldName, array('label'=>false, 'type'=>'textarea'));
                                            echo $this->Fck->load($fieldId);
                                    echo '</div>';
                                    echo '<div class="spacer"></div>';
                                }
                                else{
                                    echo $this->Form->input($fieldName, array('label'=>false, 'type' => 'textarea', 'readonly' => true));
                                }
			} elseif ($infosupdef['Infosupdef']['type'] == 'file') {
				if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]))
					echo  $this->Form->input($fieldName, array('label'=>false, 'type'=>'file', 'size'=>'60', 'title'=>$infosupdef['Infosupdef']['commentaire'],  'readonly'=> $disabled));
				else {
                    $name = $this->data['Infosup'][$infosupdef['Infosupdef']['code']];
                    if (is_array($name)) $name = $name['name'];
					echo '<span id="'.$infosupdef['Infosupdef']['code'].'InputFichier" style="display: none;"></span>';
					echo '<span id="'.$infosupdef['Infosupdef']['code'].'AfficheFichier">';
					echo '['.$this->Html->link($name, '/infosups/download/'.$this->data['Deliberation']['id'].'/'.$infosupdef['Infosupdef']['id'], array('title'=>$infosupdef['Infosupdef']['commentaire'])).']';
					echo '&nbsp;&nbsp;';
					if (!$disabled)
						echo $this->Html->link('Supprimer', "javascript:infoSupSupprimerFichier('".$infosupdef['Infosupdef']['code']."', '".$infosupdef['Infosupdef']['commentaire']."')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
					echo '</span>';
				}
			} elseif ($infosupdef['Infosupdef']['type'] == 'odtFile') {
				if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']])
                                    || empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]['tmp_name'])
                                    || isset($errors_Infosup[$infosupdef['Infosupdef']['code']]))
					echo  $this->Form->input($fieldName, array('label'=>false, 'type'=>'file', 'size'=>'60', 'title'=>$infosupdef['Infosupdef']['commentaire'], 'readonly'=> $disabled));
				else {
					echo '<span id="'.$infosupdef['Infosupdef']['code'].'InputFichier" style="display: none;"></span>';
					echo '<span id="'.$infosupdef['Infosupdef']['code'].'AfficheFichier">';
					if (Configure::read('GENERER_DOC_SIMPLE')) {
						echo '['.$this->Html->link($this->data['Infosup'][$infosupdef['Infosupdef']['code']], '/infosups/download/'.$this->data['Deliberation']['id'].'/'.$infosupdef['Infosupdef']['id'], array('title'=>$infosupdef['Infosupdef']['commentaire'],  'readonly'=> $disabled)).']';
					} else {
						$name = $this->data['Infosup'][$infosupdef['Infosupdef']['code']] ;
						if (!$disabled){
						    $url = Configure::read('PROTOCOLE_DL')."://".$_SERVER['SERVER_NAME']."/files/generee/projet/".$this->data['Deliberation']['id']."/$name"; 
                            echo $this->Form->hidden($fieldName);
                        }
                        else
                            $url = "http://".$_SERVER['SERVER_NAME']."/files/generee/projet/".$this->data['Deliberation']['id']."/$name";
						echo "[<a href='$url'>$name</a>]";
					}
					echo '&nbsp;&nbsp;';
					if (!$disabled)
						echo $this->Html->link('Supprimer', "javascript:infoSupSupprimerFichier('".$infosupdef['Infosupdef']['code']."', '".$infosupdef['Infosupdef']['commentaire']."')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
					echo '</span>';
				}
			} elseif ($infosupdef['Infosupdef']['type'] == 'list') {
                            if (!$disabled){
				echo $this->Form->input($fieldName, array('label'=>false, 'options'=>$infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty'=>true, 'title'=>$infosupdef['Infosupdef']['commentaire'], 'readonly'=> $disabled));
                            }
                            else{
                                echo $this->Form->input($fieldName, array('label'=>false, 'options'=>$infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty'=>true, 'title'=>$infosupdef['Infosupdef']['commentaire'], 'disabled'=> $disabled));
                                echo $this->Form->input($fieldName, array('id'=> false, 'type'=> 'hidden'));
                            }
                        }
		echo '</div>';
		echo '<br>';
                echo "<div class='spacer'> </div>";
	};?>
</div>
<?php endif; ?>

<?php if (Configure::read('DELIBERATIONS_MULTIPLES')) :?> 
<div id="tab5" <?php echo isset($lienTab) && $lienTab==5?'':'style="display: none;"'?>>
    <?php echo $this->element('multidelib');?>
</div>
<?php endif; ?>

<div class="spacer" style="border-top: solid 1px #e0ef90;"></div>

<div class="submit">
<?php 
echo $this->Form->hidden('Deliberation.id');

echo $this->Html->tag("div", null, array("class" => "btn-group"));
echo $this->Html->link('<i class="icon-circle-arrow-left"></i> Annuler', array('action' => 'mesProjetsRedaction'), array('class' => 'btn', 'escape' => false, 'title' => 'Annuler', 'name'=>'Annuler'));
echo $this->Form->button('<i class="icon-save"></i> Sauvegarder', array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Enregistrer le projet'));
echo $this->Html->tag('/div', null);
?>

</div>

<?php echo $this->Form->end(); ?>
<script>
// variables globales

// affichage de l'éditeur de texte intégré ckEditor
function editerTexte(obj, textId, afficheTextId) {
	$('#'+textId).ckeditor();
	$('#'+afficheTextId).hide();
	$(obj).hide();
}
</script>
