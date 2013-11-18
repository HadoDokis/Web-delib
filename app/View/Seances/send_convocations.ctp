<div class="deliberations">
<?php echo $this->Html->script('utils.js'); ?>

<h2>Envoi des convocations</h2>

<?php echo $this->Form->create( 'Seance',
                                array( 'type'=>'file',
                                       'url'=>"/seances/sendConvocations/$seance_id/$model_id")); ?>
<?php 
echo $this->Html->tag('div', null, array('style' => 'padding-right:1em;float:left;'));
$this->Html2->boutonSubmitUrl("/seances/genererConvocation/$seance_id/$model_id",'Générer les convocations','Générer les convocations', null, null,'icon-cogs');
echo $this->Html->tag('/div', null);
$this->Html2->boutonSubmitUrl("/seances/recuperer_zip/$seance_id/$model_id",'Récupérer une archive contenant les convocations','Récupérer une archive contenant les convocations',null,'btn-inverse','icon-download');
?>
<br>
<table style='width:100%'>
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
        echo '<td style="text-align: center; vertical-align: middle;">';
        if (empty($acteur['Acteur']['email']))
            echo $this->Form->checkbox('Acteur.id_'.$acteur['Acteur']['id'], array(
                        'disabled' => true,
                        'title' => 'Envoi impossible, l\'adresse mail de l\'acteur n\'est pas renseigné'));
        elseif ($acteur['Acteur']['date_envoi'] == null)
            echo $this->Form->checkbox('Acteur.id_'.$acteur['Acteur']['id']);
        else
            echo '<i class="icon-ok" title="Convocation déjà envoyée"></i>';

        echo '</td>';

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
    <?php $this->Html2->boutonSubmit('Envoyer les convocations','Envoyer les convocations','envelope'); ?>
</div>

<?php echo $this->Form->end(); ?>
</div>

<style>
    table tr td, table tr th{
        vertical-align: middle;
    }
</style>