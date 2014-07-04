<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<h2><?php echo "Envoi d'une notification  aux utilisateurs du profil : $libelle_profil"; ?></h2>
<?php
echo $this->Form->create('Profil', array('url' => array('controller' => 'profils', 'action' => 'notifier', $id)));
echo '<div class="annexesGauche"></div>';
echo '<div class="fckEditorProjet">';
echo $this->Form->input('Profil.content', array('label' => '', 'type' => 'textarea'));
echo $this->Fck->load('Profil.content');
echo '</div>';
echo '<div class="spacer"></div>';
?>
<div class="btn-group">
<?php
echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', array('action' => 'index'), array('escape' => false, 'class' => 'btn'));
echo $this->Form->button("<i class='fa fa-envelope'></i> Envoyer", array('class' => "btn btn-primary", 'escape' => false, 'title' => 'Envoyer le message aux utilisateurs de ce profil'));
?>
</div>
<?php
echo $this->Form->end();
?>
