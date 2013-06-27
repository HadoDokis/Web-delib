<div class="deliberations">
<?php echo $this->Html->script('utils.js'); ?>

<h2>Envoi de l'odre du jour</h2>

<?php echo $this->Form->create( 'Seance',
                                array( 'type'=>'file',
                                       'url'=>"/seances/sendOrdredujour/$seance_id/$model_id")); ?>
<?php 
echo $this->Html->tag('div', null, array('style' => 'padding-right:1em;float:left;'));
$this->Html2->boutonSubmitUrl("/seances/genererOrdredujour/$seance_id/$model_id",'Générer l\'ordres du jour','Générer l\'ordre du jour', null, null,'icon-cogs');
echo $this->Html->tag('/div', null);
$this->Html2->boutonSubmitUrl("/seances/recuperer_zip/$seance_id/$model_id",'Récupérer une archive contenant l\'ordre du jour','Récupérer une archive contenant l\'ordre du jour',null,'btn-inverse','icon-download');
?>
<br /><br />
<table width='100%'>
    <caption>Liste des acteurs</caption>
    <tr>
        <th></th>
        <th>Élus</th>
        <th>Document</th>
        <th>Date d'envoi</th>
        <th>statut</th>
    </tr>
<?php
    $numLigne = 1;
    foreach ($acteurs as $acteur) {
        $rowClass = ($numLigne & 1)?array('height' => '36px'):array( 'height' => '36px', 'class'=>'altrow');
        echo $this->Html->tag('tr', null, $rowClass);
        $numLigne++;
        if ($acteur['Acteur']['date_envoi'] == null)
            echo ('<td>'.$this->Form->checkbox('Acteur.id_'.$acteur['Acteur']['id']).'</td>');
        else
            echo ('<td> </td>');
        echo ('<td>'.$acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'].'</td>'); 
        
        if (file_exists(WEBROOT_PATH.'/files/seances/'.$seance_id."/$model_id/".$acteur['Acteur']['id'].'.pdf')){
            $filepath = '/files/seances/'.$seance_id."/$model_id/".$acteur['Acteur']['id'].'.pdf';
            $ext = '.pdf';
        }else if (file_exists(WEBROOT_PATH.'/files/seances/'.$seance_id."/$model_id/".$acteur['Acteur']['id'].'.odt')){
            $filepath = '/files/seances/'.$seance_id."/$model_id/".$acteur['Acteur']['id'].'.odt';
            $ext = '.odt';
        }else{
            $filepath = '';
        }
            
        
        if ($filepath != '')
            echo ('<td>'.$this->Html->link($model['Model']['modele'].$ext, $filepath).' : ['.$date_convocation.']</td>');
        else
            echo ('<td></td>');
        if ($acteur['Acteur']['date_envoi'] == null)
            echo ('<td>Non envoyé</td>');
        else
            echo ('<td>'.'Envoyé le : '.$this->Form2->ukToFrenchDateWithHour($acteur['Acteur']['date_envoi']).'</td>');

        if ($acteur['Acteur']['date_reception'] == null) {
            if ($use_mail_securise)
                echo ('<td>Non reçu</td>');
            else
                echo ('<td>Pas d\'accusé de réception</td>');
        }
        else
            echo ('<td>'.'Reçu le : '.$this->Form2->ukToFrenchDateWithHour($acteur['Acteur']['date_reception']).'</td>');
    } 
?>
</tr>
</table>
<br />
<div class="submit">
    <?php $this->Html2->boutonSubmit('Envoyer l\'ordre du jour','Envoyer l\'ordre du jour','envelope'); ?>
</div>

<?php echo $this->Form->end(); ?>
</div>
