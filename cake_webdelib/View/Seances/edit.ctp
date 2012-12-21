<?php
echo $this->Html->script('calendrier.js');
echo $this->Html->script('utils.js');
echo $this->Html->script('ckeditor/ckeditor');
echo $this->Html->script('ckeditor/adapters/jquery');

if ($this->action == 'add')
	$titre = 'Ajout d\'une s&eacute;ance';
else
	$titre = 'Modification d\'une s&eacute;ance';
echo $this->Html->tag('h1', $titre);

echo $this->Form->create('Seance',array('url'=>array('action'=>$this->action), 'type'=>'file', 'name'=>'seanceForm'));
?>

<div class='onglet'>
	<a href="#" id="emptylink" alt=""></a>
	<a href="javascript:afficheOnglet(1)" id='lienTab1' class="ongletCourant">Date de séance</a>
	<?php if (!empty($infosupdefs)): ?>
	<a href="javascript:afficheOnglet(2)" id='lienTab2'>Informations supplémentaires</a>
	<?php endif; ?>
</div>

<div id="tab1">
<div class="required">
    <label>Date : </label>
    <input name="date" size="9"
    <?php
        if (isset($date))    echo ("value =\"$date\"");
     ?>/>&nbsp;
	<a href="javascript:show_calendar('seanceForm.date','f');">	
	    <?php echo $this->Html->image("calendar.png", array('style'=>"border='0'")); ?>
	</a> &agrave;
	<?php echo $this->Form->hour('Seance.date',true, array()); ?>h<?php echo $this->Form->minute('Seance.date', array()); ?>min
</div>
<br />

<div class="required">
    <?php echo $this->Form->input('Seance.type_id', array('label'=>'Type de s&eacute;ance',
                                                    'options'=>$typeseances, 
                                                    'default'=>$this->Html->value('Seance.type_id'), 
                                                    'empty'=>true));
    ?>
</div>

<br />

</div>
<?php if (!empty($infosupdefs)): ?>
<div id="tab2"  style="display: none;">
<?php
	foreach($infosupdefs as $infosupdef) {
	    $fieldName = 'Infosup.'.$infosupdef['Infosupdef']['code'];
	    $fieldId = 'Infosup'.Inflector::camelize($infosupdef['Infosupdef']['code']);
	    echo "<div class='required'>";
	            echo $this->Form->label($fieldName, $infosupdef['Infosupdef']['nom'], array('name'=>'label'.$infosupdef['Infosupdef']['code']));
	            if ($infosupdef['Infosupdef']['type'] == 'text') {
	                    echo $this->Form->input($fieldName, array('label'=>'', 'size'=>$infosupdef['Infosupdef']['taille'], 'type' => 'textarea', 'title'=>$infosupdef['Infosupdef']['commentaire']));
	            } elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
	                    echo $this->Form->input($fieldName, array('label'=>'', 'type'=>'checkbox', 'title'=>$infosupdef['Infosupdef']['commentaire']));
	            } elseif ($infosupdef['Infosupdef']['type'] == 'date') {
	                    echo $this->Form->input($fieldName, array('type'=>'text', 'div'=>false, 'label'=>'', 'size'=>'9', 'title'=>$infosupdef['Infosupdef']['commentaire']));
	                    echo '&nbsp;';
	                    echo $this->Html->link($this->Html->image("calendar.png", array('style'=>"border='0'")), "javascript:show_calendar('seanceForm.$fieldId', 'f');", array('escape'=> false), false);
	            } elseif ($infosupdef['Infosupdef']['type'] == 'richText') {
	                    echo '<div class="annexesGauche"></div>';
	                    echo '<div class="fckEditorProjet">';
	                            echo $this->Form->input($fieldName, array('label'=>'', 'type'=>'textarea'));
	                            echo $this->Fck->load($fieldId);
	                    echo '</div>';
	                    echo '<div class="spacer"></div>';
	            } elseif ($infosupdef['Infosupdef']['type'] == 'file') {
	                    if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]))
	                            echo  $this->Form->input($fieldName, array('label'=>'', 'type'=>'file', 'size'=>'60', 'title'=>$infosupdef['Infosupdef']['commentaire']));
	                    else {
	                            echo '<span id="'.$infosupdef['Infosupdef']['code'].'InputFichier" style="display: none;"></span>';
	                            echo '<span id="'.$infosupdef['Infosupdef']['code'].'AfficheFichier">';
	                            echo '['.$this->Html->link($this->data['Infosup'][$infosupdef['Infosupdef']['code']], '/infosups/download/'.$this->data['Seance']['id'].'/'.$infosupdef['Infosupdef']['id'], array('title'=>$infosupdef['Infosupdef']['commentaire'])).']';
	                            echo '&nbsp;&nbsp;';
	                            echo $this->Html->link('Supprimer', "javascript:infoSupSupprimerFichier('".$infosupdef['Infosupdef']['code']."', '".$infosupdef['Infosupdef']['commentaire']."')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
	                            echo '</span>';
	                    }
	       } elseif ($infosupdef['Infosupdef']['type'] == 'odtFile') {
	                    if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]))
	                            echo  $this->Form->input($fieldName, array('label'=>'', 'type'=>'file', 'size'=>'60', 'title'=>$infosupdef['Infosupdef']['commentaire']));
	                    else {
	                            echo '<span id="'.$infosupdef['Infosupdef']['code'].'InputFichier" style="display: none;"></span>';
	                            echo '<span id="'.$infosupdef['Infosupdef']['code'].'AfficheFichier">';
	                            if (Configure::read('GENERER_DOC_SIMPLE')) {
	                                    echo '['.$this->Html->link($this->data['Infosup'][$infosupdef['Infosupdef']['code']], '/infosups/download/'.$this->data['Deliberation']['id'].'/'.$infosupdef['Infosupdef']['id'], array('title'=>$infosupdef['Infosupdef']['commentaire'])).']';
	                            } else {
	                                    $name = $this->data['Infosup'][$infosupdef['Infosupdef']['code']] ;
	                                    $url = Configure::read('PROTOCOLE_DL')."://".$_SERVER['SERVER_NAME']."/files/generee/seance/".$this->data['Seance']['id']."/$name";
	                                    echo "<a href='$url'>$name</a> ";
	                            }
	                            echo '&nbsp;&nbsp;';
	                            echo $this->Html->link('Supprimer', "javascript:infoSupSupprimerFichier('".$infosupdef['Infosupdef']['code']."', '".$infosupdef['Infosupdef']['commentaire']."')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
	                            echo '</span>';
	                    }
	            } elseif ($infosupdef['Infosupdef']['type'] == 'list') {
	                    echo $this->Form->input($fieldName, array('label'=>'', 'options'=>$infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty'=>true, 'title'=>$infosupdef['Infosupdef']['commentaire']));
	            }
	    echo '</div>';
	    echo '<br>';
	};
?>
</div>
<?php endif; ?>
<!--
<ul class="actions">
	<li><?php echo $this->Html->link('Supprimer','/seances/delete/' . $this->Html->value('Seance.id'), null, 'Etes-vous sur de vouloir supprimer la seance du "' . $this->Html->value('Seance.date').'" ?');?> 
	
	
	<li><?php echo $this->Html->link('Annuler', '/seances/index')?></li>
</ul>
-->
<div class="submit">
	<?php echo $this->Form->hidden('Seance.id');?>
	<?php echo $this->Form->submit('Enregistrer', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $this->Html->link('Annuler', '/seances/listerFuturesSeances', array('class'=>'link_annuler', 'title'=>'Annuler'))?>
</div>

<?php echo $this->Form->end(); ?>
