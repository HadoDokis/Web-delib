<?php 
    echo $this->Html->script('utils.js'); 
    echo $this->Html->script('ckeditor/ckeditor'); 
    echo $this->Html->script('ckeditor/adapters/jquery'); 
?>


<h2><?php echo "Envoi d'une notification  aux utilisateurs du profil : $libelle_profil"; ?></h2>
<script>
    document.getElementById("pourcentage").style.display='none';
    document.getElementById("progrbar").style.display='none';
    document.getElementById("affiche").style.display='none';
    document.getElementById("contTemp").style.display='none';
</script>


<?php
    echo $this->Form->create('Profil', array('url'=>'/profils/notifier/'.$id)); 
    echo '<div class="annexesGauche"></div>';
    echo '<div class="fckEditorProjet">';
    echo $this->Form->input('Profil.content', array('label'=>'', 'type'=>'textarea'));
    echo $this->Fck->load('Profil.content');
    echo '</div>';
    echo '<div class="spacer"></div>';
//    echo $this->Form->submit('Envoyer');
    $this->Html2->boutonSubmit("Envoyer");
    echo $this->Form->end();
?>
