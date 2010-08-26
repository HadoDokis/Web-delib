<div id="vue_cadre">
    <h3>Renvoyer le projet &agrave;</h3>

    <?php echo $form->create('Deliberation',array('url'=>'/deliberations/retour/'.$delib_id,'type'=>'post')); ?>
    <table id='retour' >
    <tr>
    <?php
        for ($i = 0; $i< $nbTraitements; $i++)
	    echo '<td align="center">'. $html->image('/img/icons/user.png') .'</td>';

        echo ('</tr><tr>');
        foreach ($liste as $i => $nom)
            echo "<td><input type='radio' name='data[Deliberation][radio]' id='radio_$i' value='$i'  />".$nom."</td>";
       
        
    ?>
    </tr>
    </table>
   <br> <br> <br> 
    
    <?php echo $form->submit('Envoyer',array('div'=>false)); ?>
    
	<?php echo $form->end(); ?>
</div>
