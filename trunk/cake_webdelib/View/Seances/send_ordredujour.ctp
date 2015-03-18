<?php
echo $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min') .
     $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.fr') .
     $this->Html->css('/components/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');
     $this->Html->addCrumb('Liste des présents');//, array($this->request['controller'], 'action'=>'index'));
     
   echo $this->Bs->div('deliberations').
        $this->Bs->tag('h2', 'Envoi de l\'ordre du jour') .

        //debut du form
        $this->Form->create('Seance', array('url' => array('controller' => 'seances', 'action' => 'sendOrdredujour', $seance_id, $model_id), 'class'=>'waiter', 'data-modal' => 'Envoi de l\'ordre du jour')) .
        $this->Bs->div('boutons_generation_odj') .
            $this->Bs->div('btn-group').
            $this->Bs->btn('Générer l\'ordre du jour',
            array('controller'=>'seances', 
                'action'=>'genereFusionToFiles', 
                $seance_id,
                $model_id,
                'ordredujour'
                ), 
            array(
                'class' => "btn btn-success waiter", 
                'escape' => false, 
                'title' => 'Générer l\'ordre du jour', 
                'data-modal' => 'Génération de l\'ordre du jour en cours', 
                'type'=>'primary',
                'icon'=>'fa fa-cogs')).
            $this->Bs->btn('Télécharger une archive contenant tous les ODJ',
            array('controller'=>'seances', 
                'action'=>'downloadZip', 
                $seance_id,
                $model_id,
                'ordredujour'
                ),
            array(
                'class' => "btn btn-inverse", 
                'escape' => false, 
                'title' => 'Récupérer une archive contenant les ordres du jour', 
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
         elseif (empty($acteur['Acteur']['link']))
              $cell_checkbox = $this->BsForm->checkbox('Acteur.id_' . $acteur['Acteur']['id'], array(
             'label' =>false,
             'disabled' => true,
             'title' => "Impossible d'envoyer à cet acteur, l'ordre du jour n'a pas encore été générée."));
         elseif ($acteur['Acteur']['date_envoi'] == null)
             $cell_checkbox = $this->BsForm->checkbox('Acteur.id_' . $acteur['Acteur']['id'], array(
             'label' =>false,
             'class' => 'checkbox_liste',
             'title' => "Impossible d'envoyer à cet acteur, l'ordre du jour n'a pas encore été générée."));
         else
             $cell_checkbox = '<i class="fa fa-check" title="ODJ déjà envoyée"></i>';

         //cellule élu
             $cell_elu = $this->Html->link($acteur['Acteur']['prenom'] . ' ' . $acteur['Acteur']['nom'], array('controller' => 'acteurs', 'action' => 'view', $acteur['Acteur']['id']));
         
         //cellule link
         if (isset($acteur['Acteur']['link']))
            $cell_link = $this->Bs->btn('Télécharger', $acteur['Acteur']['link'],
            array(
                'escape' => false, 
                'type'=>'default',
                'icon'=>'fa fa-file-pdf-o'));
         else
             $cell_link = 'Pas de document';

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
         echo $this->Bs->cell($cell_checkbox).$this->Bs->cell($cell_elu).$this->Bs->cell($cell_link).$this->Bs->cell($cell_envoi).$this->Bs->cell($cell_reception);
     }
     echo $this->Bs->endTable();  
     
     //spacer
     echo $this->Bs->div('spacer') . $this->Bs->close();
     
     echo $this->Html2->btnSaveCancel('', $previous, 'Envoyer l\'ordre du jour', 'Envoyer l\'ordre du jour');
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
        var nbChecked = $('input[type=checkbox].checkbox_liste:checked').length;
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