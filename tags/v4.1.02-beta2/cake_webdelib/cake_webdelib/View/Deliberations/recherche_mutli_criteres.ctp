<?php echo $this->Html->script('calendrier.js'); ?>
<?php $afficheNote = false; ?>

<h2><?php echo $titreVue;?></h2>
<?php echo $this->Form->create('Deliberation',array('type'=>'file','url'=>$action,  'name'=>'Deliberation')); ?>

<div id="add_form">
<table class="sample">
    <tr>
            <td><?php echo $this->Form->input('Deliberation.id', array( 'type'    => 'text', 
                                                                  'between' => '</td><td>',
                                                                  'label'   => 'Identifiant du projet',
                                                                  'size'    => '20'));?>
            </td>
    </tr>
    <tr>
         <td><?php echo $this->Form->input('Deliberation.typeacte_id', array('label'   =>'Nature',
                                                                 'options' =>$this->Session->read('user.Nature'),
                                                                 'empty'   =>true,
                                                                 'between'=>'</td><td>',
                                                                 'escape'  =>false)); ?>
            </td>
    </tr>
    <tr>
            <td><?php echo $this->Form->input('Deliberation.rapporteur_id', array( 'between' => '</td><td>', 
                                                                             'label'   => 'Rapporteur', 
                                                                             'options' => $rapporteurs, 
                                                                             'empty'   => true));?>
            </td>
    </tr>
    <tr>
            <td><?php echo $this->Form->input('Deliberation.seance_id', array( 'between'  => '</td><td>',
                                                                         'label'    => 'Date s&eacute;ance (et) ', 
                                                                         'options'  => $date_seances, 
                                                                         'multiple' => true,
                                                                         'empty'    => false, 
                                                                         'size'     => '10'));?>
            </td>
    </tr>
    <tr>
            <td><?php echo $this->Form->input('Deliberation.texte', array( 'between' => '</td><td>',
                                                                     'label'   => 'Libell&eacute; *',
                                                                     'size' => '30'));?>
            </td>
    </tr>
    <tr>
            <td><?php echo $this->Form->input('Deliberation.service_id', array('between'=>'</td><td>','label'=>'Service Emetteur', 'options'=>$services, 'empty'=>true, 'escape'=>false));?></td>
    </tr>
    <tr>
            <td><?php echo $this->Form->input('Deliberation.theme_id', array('between'=>'</td><td>','label'=>'Thème ', 'options'=>$themes, 'default'=>$this->Html->value('Deliberation.theme_id'), 'empty'=>true));?></td>
    </tr>
    <tr>
            <td><?php echo $this->Form->input('Deliberation.circuit_id', array('between'=>'</td><td>','label'=>'Circuit ', 'options'=>$circuits, 'default'=>$this->Html->value('Deliberation.circuit_id'), 'empty'=>true));?></td>
    </tr>
    <tr>
            <td><?php echo $this->Form->input('Deliberation.etat', array('between'=>'</td><td>','label'=>'Etat ', 'options'=> $etats, 'default'=>$this->Html->value('Deliberation.etat'), 'empty'=>true));?></td>
    </tr>
	<?php foreach($infosupdefs as $infosupdef) {
		$fieldName = 'Infosup.'.$infosupdef['Infosupdef']['id'];
		echo '<tr>';
			echo '<td>'.$this->Form->label($fieldName, $infosupdef['Infosupdef']['nom'].($infosupdef['Infosupdef']['type'] == 'date' ? '' : ' *')).'</td>';
			echo '<td>';
			if ($infosupdef['Infosupdef']['type'] == 'text' || $infosupdef['Infosupdef']['type'] == 'richText') {
				echo $this->Form->input($fieldName, array('label'=>false, 'size'=>$infosupdef['Infosupdef']['taille'], 'title'=>$infosupdef['Infosupdef']['commentaire']));
				$afficheNote = true;
			} elseif ($infosupdef['Infosupdef']['type'] == 'date') {
				echo $this->Form->input($fieldName, array('label'=>false, 'size'=>'9', 'div'=>false, 'title'=>$infosupdef['Infosupdef']['commentaire']));
				echo '&nbsp;';
				$fieldId = "'Deliberation.Infosup".Inflector::camelize($infosupdef['Infosupdef']['id'])."'";
				echo $this->Html->link($html->image("calendar.png", array('style' => "border='0'")), "javascript:show_calendar($fieldId, 'f');", array(), false, false);
			} elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
				echo $this->Form->input($fieldName, array('label'=>false, 'options'=>$listeBoolean, 'empty'=>true));
			} elseif ($infosupdef['Infosupdef']['type'] == 'list') {
				echo $this->Form->input($fieldName, array('label'=>false, 'options'=>$infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty'=>true));
			}
			echo '</td>';
		echo '</tr>';
	} ?>
        <tr>
           <td><?php echo $this->Form->label('Deliberation.generer','Générer le document'); ?> </td>
           <td><?php echo $this->Form->input('Deliberation.generer', array('type'=>'checkbox', 'label'=>false, 'div'=> false, 'onClick'=>"if(this.checked) $('#DeliberationModel').show(); else $('#DeliberationModel').hide(); "));?>
               <?php echo $this->Form->input('Deliberation.model', array('label'=>false, 'options'=>$models, 'div' => false, 'style'=> 'display:none;'));?>
           </td>
    </tr>
</table>
<br />
<?php
echo $this->Form->button('<i class="icon-search"></i> Rechercher', array('type' => 'submit', 'div' => false, 'class'=>'btn btn-primary', 'name'=>'Rechercher')); 
?>
</div>
<br />
<?php if ($afficheNote): ?>
<p>* : le caractère % permet d'affiner les recherches comme indiqué ci-dessous :
	<ul>
		<li>Commence par : texte% (si on recherche une information qui commence par 'Département' on écrit comme critère de recherche : Département%)</li>
		<li>Comprend : %texte% (si on recherche une information qui comprend 'avril' on écrit comme critère de recherche : %avril%)</li>
		<li>Finit par : %texte (si on recherche une information qui finit par 'clos.' on écrit comme critère de recherche : %clos.)</li>
	</ul>
</p>
<?php endif; ?>
