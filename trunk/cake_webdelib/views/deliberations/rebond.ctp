<div id="vue_cadre">

<?php
    echo $form->create('Deliberation',array('url'=>'/deliberations/rebond/'.$delib_id,'type'=>'post'));
    echo $form->select('user', $users, array('title'=>"A qui voulez vous l'envoyer ? : "));
    echo $form->submit('Envoyer',array('div'=>false));
    echo $form->end(); 
?>
</div>
