<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<h2><?php echo "Envoi d'une notification  aux utilisateurs du profil : $libelle_profil"; ?></h2>
<?php
    echo $this->Form->create('Profil', array('url'=>array('controller'=>'profils', 'action'=>'notifier',$id)));
    echo '<div class="annexesGauche"></div>';
    echo '<div class="fckEditorProjet">';
    echo $this->Form->input('Profil.content', array('label'=>'', 'type'=>'textarea'));
    echo $this->Fck->load('Profil.content');
    echo '</div>';
    echo '<div class="spacer"></div>';
    $this->Html2->boutonSubmit("Envoyer");
    echo $this->Form->end();
?>
