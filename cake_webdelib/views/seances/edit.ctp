<?php echo $javascript->link('utils.js'); ?>
<?php echo $javascript->link('ckeditor/ckeditor'); ?>
<?php echo $javascript->link('ckeditor/adapters/jquery'); ?>

<h1>Modification d'une s&eacute;ance</h1>
<?php echo $form->create('Seance',array('url'=>'/seances/edit/'.$html->value('Seance.id'),'type'=>'post')); ?>

<div class='onglet'>
        <a href="#" id="emptylink" alt=""></a>
        <a href="javascript:afficheOnglet(1)" id='lienTab1' class="ongletCourant">Date de séance</a>
        <a href="javascript:afficheOnglet(2)" id='lienTab2'>Informations supplémentaires</a>
</div>

<div id="tab1">
<div class="required">
    <?php echo $form->label('Seance.date', 'Date');?>
    <?php echo $form->day('Seance.date',null,null,false); ?>-
    <?php echo $form2->monthOptionTagFr('Seance.date',null,$html->value('Seance.date'),null,null,false); ?>-
    <?php echo $form->year('Seance.date',2009,date('Y')+10); ?>&agrave; 
    <?php echo $form->hour('Seance.date',true, null, null); ?>h
    <?php echo $form->minute('Seance.date',null,null,false); ?>min
</div>
<br />

<div class="required">
    <?php echo $form->input('Seance.type_id', array('label'=>'Type de s&eacute;ance',
                                                    'options'=>$typeseances, 
                                                    'default'=>$html->value('Seance.type_id'), 
                                                    'empty'=>true));
    ?>
</div>

<br />

</div>
<div id="tab2"  style="display: none;">
        <?php
        foreach($infosupdefs as $infosupdef) {
                $fieldName = 'Infosup.'.$infosupdef['Infosupdef']['code'];
                $fieldId = 'Infosup'.Inflector::camelize($infosupdef['Infosupdef']['code']);
                echo "<div class='required'>";
                        echo $form->label($fieldName, $infosupdef['Infosupdef']['nom'], array('name'=>'label'.$infosupdef['Infosupdef']['code']));
                        if ($infosupdef['Infosupdef']['type'] == 'text') {
                                echo $form->input($fieldName, array('label'=>'', 'size'=>$infosupdef['Infosupdef']['taille'], 'title'=>$infosupdef['Infosupdef']['commentaire']));
                        } elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
                                echo $form->input($fieldName, array('label'=>'', 'type'=>'checkbox', 'title'=>$infosupdef['Infosupdef']['commentaire']));
                        } elseif ($infosupdef['Infosupdef']['type'] == 'date') {
                                echo $form->input($fieldName, array('type'=>'text', 'div'=>false, 'label'=>'', 'size'=>'9', 'title'=>$infosupdef['Infosupdef']['commentaire']));
                                echo '&nbsp;';
                                echo $html->link($html->image("calendar.png", array('style'=>"border='0'")), "javascript:show_calendar('Deliberation.$fieldId', 'f');", array(), false, false);
                        } elseif ($infosupdef['Infosupdef']['type'] == 'richText') {
                                echo '<div class="annexesGauche"></div>';
                                echo '<div class="fckEditorProjet">';
                                        echo $form->input($fieldName, array('label'=>'', 'type'=>'textarea'));
                                        echo $fck->load($fieldId);
                                echo '</div>';
                                echo '<div class="spacer"></div>';
                        } elseif ($infosupdef['Infosupdef']['type'] == 'file') {
                                if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]))
                                        echo  $form->input($fieldName, array('label'=>'', 'type'=>'file', 'size'=>'60', 'title'=>$infosupdef['Infosupdef']['commentaire']));
                                else {
                                        echo '<span id="'.$infosupdef['Infosupdef']['code'].'InputFichier" style="display: none;"></span>';
                                        echo '<span id="'.$infosupdef['Infosupdef']['code'].'AfficheFichier">';
                                        echo '['.$html->link($this->data['Infosup'][$infosupdef['Infosupdef']['code']], '/infosups/download/'.$this->data['Deliberation']['id'].'/'.$infosupdef['Infosupdef']['id'], array('title'=>$infosupdef['Infosupdef']['commentaire'])).']';
                                        echo '&nbsp;&nbsp;';
                                        echo $html->link('Supprimer', "javascript:infoSupSupprimerFichier('".$infosupdef['Infosupdef']['code']."', '".$infosupdef['Infosupdef']['commentaire']."')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
                                        echo '</span>';
                                }
                   } elseif ($infosupdef['Infosupdef']['type'] == 'odtFile') {
                                if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]))
                                        echo  $form->input($fieldName, array('label'=>'', 'type'=>'file', 'size'=>'60', 'title'=>$infosupdef['Infosupdef']['commentaire']));
                                else {
                                        echo '<span id="'.$infosupdef['Infosupdef']['code'].'InputFichier" style="display: none;"></span>';
                                        echo '<span id="'.$infosupdef['Infosupdef']['code'].'AfficheFichier">';
                                        if (Configure::read('GENERER_DOC_SIMPLE')) {
                                                echo '['.$html->link($this->data['Infosup'][$infosupdef['Infosupdef']['code']], '/infosups/download/'.$this->data['Deliberation']['id'].'/'.$infosupdef['Infosupdef']['id'], array('title'=>$infosupdef['Infosupdef']['commentaire'])).']';
                                        } else {
                                                $name = $this->data['Infosup'][$infosupdef['Infosupdef']['code']] ;
                                                $url = Configure::read('PROTOCOLE_DL')."://".$_SERVER['SERVER_NAME']."/files/generee/projet/".$this->data['Deliberation']['id']."/$name";
                                                echo "<a href='$url'>$name</a> ";
                                        }
                                        echo '&nbsp;&nbsp;';
                                        echo $html->link('Supprimer', "javascript:infoSupSupprimerFichier('".$infosupdef['Infosupdef']['code']."', '".$infosupdef['Infosupdef']['commentaire']."')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
                                        echo '</span>';
                                }
                        } elseif ($infosupdef['Infosupdef']['type'] == 'list') {
                                echo $form->input($fieldName, array('label'=>'', 'options'=>$infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty'=>true, 'title'=>$infosupdef['Infosupdef']['commentaire']));
                        }
                echo '</div>';
                echo '<br>';
        };?>
</div>
<!--
<ul class="actions">
	<li><?php echo $html->link('Supprimer','/seances/delete/' . $html->value('Seance.id'), null, 'Etes-vous sur de vouloir supprimer la seance du "' . $html->value('Seance.date').'" ?');?> 
	
	
	<li><?php echo $html->link('Annuler', '/seances/index')?></li>
</ul>
-->
<div class="submit">
	<?php echo $form->hidden('Seance.id');?>
	<?php echo $form->submit('Enregistrer', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', '/seances/listerFuturesSeances', array('class'=>'link_annuler', 'title'=>'Annuler'))?>
</div>

<?php echo $form->end(); ?>
