<?php 
    echo $javascript->link('utils.js'); 
    echo $javascript->link('ckeditor/ckeditor'); 
    echo $javascript->link('ckeditor/adapters/jquery'); 
?>


<h2><?php echo "Envoi d'une notification  aux utilisateurs du profil : $libelle_profil"; ?></h2>
<script>
    document.getElementById("pourcentage").style.display='none';
    document.getElementById("progrbar").style.display='none';
    document.getElementById("affiche").style.display='none';
    document.getElementById("contTemp").style.display='none';
</script>


<?php
    echo $form->create('Profil', array('url'=>'/profils/notifier/'.$id)); 
    echo '<div class="annexesGauche"></div>';
    echo '<div class="fckEditorProjet">';
    echo $form->input('Profil.content', array('label'=>'', 'type'=>'textarea'));
    echo $fck->load('Profil.content');
    echo '</div>';
    echo '<div class="spacer"></div>';
    echo $form->submit('Envoyer');
    echo $form->end();
?>
