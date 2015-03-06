<?php
echo $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min') .
     $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.fr') .
     $this->Html->css('/components/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');
     $this->Html->addCrumb('Liste des présents');//, array($this->request['controller'], 'action'=>'index'));
     
   echo $this->Bs->div('deliberations').
        $this->Bs->tag('h2', 'Envoi des convocations') .

        //debut du form
        $this->Form->create('Seance', array('url' => array('controller' => 'seances', 
            'action' => 'sendConvocations', $seance_id, $model_id), 
            'class'=>'waiter', 'data-modal' => 'Envoi des convocations')) .
        $this->Bs->div('boutons_generation_convocation') .
            $this->Bs->div('btn-group').
            $this->Bs->btn('Générer les convocations',
            array('controller'=>'seances', 
                'action'=>'genereFusionToFiles', 
                $seance_id,
                $model_id,
                'convocation'
                ), 
            array(
                'class' => "btn btn-success waiter", 
                'escape' => false, 
                'title' => 'Générer le document des convocations', 
                'data-modal' => 'Génération des convocations en cours', 
                'type'=>'primary',
                'icon'=>'fa fa-cogs')).
            $this->Bs->btn('Télécharger une archive contenant toutes les convocations',
            array('controller'=>'seances', 
                'action'=>'downloadZip', 
                $seance_id,
                $model_id,
                'convocation'
                ), 
            array(
                'class' => "btn btn-inverse", 
                'escape' => false, 
                'title' => 'Récupérer une archive contenant les convocations', 
                'type'=>'primary',
                'icon'=>'fa fa-download')) .
        $this->Bs->close(3);

     //spacer
     $this->Bs->div('spacer') . $this->Bs->close();
        
     //creation du tableau
     $attribute = array();
     $attribute['attributes']['name'] = 'tableListeActeur';
     echo $this->Bs->tag('h2', 'Liste des acteurs') .
     $this->Bs->lineAttributes(array('class'=>'colonne_checkbox'));
     $this->Bs->setTableNbColumn(4);
     echo $this->Bs->table(
     array(
        array('title' => __($this->BsForm->checkbox('masterCheckbox', array(
         'label' =>false,
         //'checked'=>$selected
         )))),
        array('title' => __('Élus')),
        array('title' => __('Document')),
        array('title' => __('Date d\'envoi')),
        array('title' => __('Statut'))
     ), array('hover', 'striped'));
     foreach ($acteurs as $acteur) {
         //cellule checkbox
         if (empty($acteur['Acteur']['email']))
              $cell_checkbox = $this->BsForm->checkbox('Acteur.id_' . $acteur['Acteur']['id'], array(
             'label' =>false,
             'disabled' => true,
             'title' => 'Envoi impossible, l\'adresse mail de l\'acteur n\'est pas renseigné'));
         elseif (empty($acteur['Acteur']['fichier']))
              $cell_checkbox = $this->BsForm->checkbox('Acteur.id_' . $acteur['Acteur']['id'], array(
             'label' =>false,
             'disabled' => true,
             'title' => "Impossible d'envoyer à cet acteur, la convocation n'a pas encore été générée."));
         elseif ($acteur['Acteur']['date_envoi'] == null)
             $cell_checkbox = $this->BsForm->checkbox('Acteur.id_' . $acteur['Acteur']['id'], array(
             'label' =>false,
             'class' => 'checkbox_acteur_convoc',
             'title' => "Impossible d'envoyer à cet acteur, la convocation n'a pas encore été générée."));
         else
             $cell_checkbox = '<i class="fa fa-check" title="Convocation déjà envoyée"></i>';

         //cellule élu
             $cell_elu = $this->Html->link($acteur['Acteur']['prenom'] . ' ' . $acteur['Acteur']['nom'], array('controller' => 'acteurs', 'action' => 'view', $acteur['Acteur']['id']));
         
         //cellule fichier
         if (isset($acteur['Acteur']['fichier']))
             $cell_fichier = $this->Bs->btn('Télécharger', $acteur['Acteur']['fichier'],
            array(
                'escape' => false, 
                'type'=>'default',
                'icon'=>'fa fa-file-pdf-o'));
         else
             $cell_fichier = 'Pas de document';

         //cellule date envoi
         if ($acteur['Acteur']['date_envoi'] == null)
             $cell_envoi = __('Non envoyé');
         else
             $cell_envoi = __('Envoyé le : ') . $this->Form2->ukToFrenchDateWithHour($acteur['Acteur']['date_envoi']);

         //cellule reception
         if ($acteur['Acteur']['date_reception'] == null) {
             if ($use_mail_securise)
                 $cell_reception = __('Non reçu');
             else
                 $cell_reception = __('Pas d\'accusé de réception');
         } else {
             $cell_reception = __('Reçu le : ') . $this->Form2->ukToFrenchDateWithHour($acteur['Acteur']['date_reception']);
         }
         echo $this->Bs->cell($cell_checkbox).$this->Bs->cell($cell_elu).$this->Bs->cell($cell_fichier).$this->Bs->cell($cell_envoi).$this->Bs->cell($cell_reception);
     }
     echo $this->Bs->endTable();  
     
     //spacer
     echo $this->Bs->div('spacer') . $this->Bs->close();
     
     echo $this->Html2->btnSaveCancel('', $previous, 'Envoyer les convocations', 'Envoyer les convocations');
     $this->Form->end() .
$this->Bs->close();
?>

<script type="text/javascript">
    $(document).ready(function () {
        //Lors d'action sur une checkbox :
        $('input[type=checkbox]').change(selectionChange);
        selectionChange();
    });
    function selectionChange() {
        var nbChecked = $('input[type=checkbox].checkbox_acteur_convoc:checked').length;
        //Apposer ou non la class disabled au bouton selon si des checkbox sont cochées (style)
        if (nbChecked > 0) {
            $('#boutonValider').removeClass('disabled');
            $("#boutonValider").prop("disabled", false);
        } else {
            $('#boutonValider').addClass('disabled');
            $("#boutonValider").prop("disabled", true);
        }
        $('#nbActeursChecked').text('(' + nbChecked + ')');
    }
</script>