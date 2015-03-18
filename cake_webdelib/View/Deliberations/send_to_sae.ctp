<?php
echo $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min') .
     $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.fr') .
     $this->Html->css('/components/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');
     $this->Html->addCrumb('Versement SAE');//, array($this->request['controller'], 'action'=>'index'));

echo $this->Bs->div('deliberations').
     //debut du form
     $this->Form->create('Seance', array(
         'url' => array(
             'controller' => 'deliberation', 
             'action' => 'sendToSae'), 
         'class'=>'waiter', 
         'data-modal' => 'Envoi de l\'ordre du jour')) .
     $this->Bs->tag('h2', 'Versement SAE') .
     $this->Bs->lineAttributes(array('class'=>'colonne_checkbox')) .
     $this->Bs->setTableNbColumn(4) .
     $this->Bs->table(
     array(
        array('title' => __($this->BsForm->checkbox('masterCheckbox', array(
         'label' =>false,
         //'checked'=>$selected
         )))),
        array('title' => __('Id')),
        array('title' => __('Numéro Délibération')),
        array('title' => __('Libellé de l\'acte')),
        array('title' => __('Classification')),
        array('title' => __('Statut'))
     ), array('hover', 'striped'));
          
     foreach ($deliberations as $delib) {
         //cellule checkbox
          if ($delib['Deliberation']['sae_etat'] == null)
          {
              $cell_checkbox = $this->BsForm->checkbox(
                      'Deliberation.id_' . $delib['Deliberation']['id'], array(
                            'label' =>false,
                            'class' => 'checkbox_liste',
                            ));
          }
          
         //cellule deliberation id
         $cell_delib_id =  $delib['Deliberation']['id'];


         //cellule link
         if (!empty($delib['Deliberation']['id']))
            $cell_link = $this->Bs->btn('Télécharger' , array('controller'=>'deliberations',
                                            'action'=>'downloadDelib', 
                                            $delib['Deliberation']['id']), array(
            'type'=>'default',
            'class'=>'media-left',
            'icon'=>'glyphicon glyphicon-download',
            ));
         else
             $cell_link = 'Pas de document';

         //cellule libellé 
         $cell_obj_delib = $delib['Deliberation']['objet_delib'];
        
         //cellule classification
         if (Configure::read('SAE') == 'PASTELL' && empty($delib['Deliberation']['sae_etat'])) {
            if (empty($nomenclatures)) $nomenclatures = array();
            $cell_nomenclature = $this->BsForm->select('Deliberation.' . $delib['Deliberation']['id'] . '_num_pref', $nomenclatures, array(
                'class' => 'select2 selectone',
                'label' => false,
                'inline' => true,
                'autocomplete' => 'off',
                'readonly' => empty($nomenclatures),
                'default' => $delib['Deliberation']['num_pref'],
                'selected' => !empty($present['Listepresence']['suppleant_id']) ? $present['Listepresence']['suppleant_id'] : NULL
            ));
        }else
        $cell_nomenclature = !empty($delib['Deliberation']['num_pref']) ? $delib['Deliberation']['num_pref'] . ' - ' . $delib['Deliberation']['num_pref_libelle'] : '<em>-- Manquante --</em>';

        //dernier statut
        if ($delib['Deliberation']['sae_etat'] == 1) {
           $cell_sae_etat = "Versé au SAE";
        } else {
           $cell_sae_etat = '&nbsp;';
        }

        echo $this->Bs->cell($cell_checkbox).$this->Bs->cell($cell_delib_id).$this->Bs->cell($cell_link).$this->Bs->cell($cell_obj_delib).$this->Bs->cell($cell_nomenclature).$this->Bs->cell($cell_sae_etat);
     }
     echo $this->Bs->endTable() .
     
     //paginate
     $this->Bs->div('paginate col-md-offset-5') . 
     $this->Paginator->prev('« Précédent ', null, null, array('tag' => 'span', 'class' => 'disabled')) .
     $this->Paginator->numbers() .
     $this->Paginator->next(' Suivant »', null, null, array('tag' => 'span', 'class' => 'disabled')) .
     $this->Paginator->counter(array('format' => 'Page %page% sur %pages%')) .
     $this->Bs->close() .
     
      //spacer
     $this->Bs->div('spacer') . $this->Bs->close() .
     $this->Bs->div('spacer') . $this->Bs->close();
     
     $this->BsForm->setLeft(5);
echo $this->Html2->btnSaveCancel('', $previous, 'Envoyer', 'Envoyer');
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