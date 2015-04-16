<?php
echo $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min');
echo $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.fr');
echo $this->Html->script('main.js');
echo $this->Html->css('/components/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');
$this->Html->addCrumb(__('Recherche multi-critères'));

echo $this->Bs->tag('h3', 'Recherche multi-critères');

//$affichage .= $this->Bs->row();
//$affichage .= $this->Bs->col('lg12');
echo $this->BsForm->create('Deliberation', array('type' => 'file', 
    'url' => array('admin' => false, 'prefix' => null, 'controller' => 'deliberations', 'action' => 'search'), 
    'name' => 'Deliberation', 
    'class' => 'form-horizontal waiter',
    'data-modal' => 'Recherche en cours'));

echo $this->Bs->div('well');


//Formulaire par défault
echo $this->BsForm->input('Deliberation.id', array(
            'type' => 'number',
            'min' => 1,
            'label' => 'Identifiant du projet',
        )).$this->BsForm->input('Deliberation.texte', array(
            'label' => 'Libellé',
        )).$this->BsForm->select('Deliberation.typeacte_id', $this->Session->read('user.Nature'), array(
            'label' => 'Nature',
            'required' => false,
            'empty' => true,
            'escape' => false
        )).$this->BsForm->select('Deliberation.rapporteur_id', $rapporteurs, array(
            'label' => 'Rapporteur',
            'required' => false,
            'empty' => true)).$this->BsForm->select('Deliberation.seance_id', $date_seances, array(
            'label' => 'Date séance (et) ',
            'multiple' => true,
            'required' => false,
            'empty' => false,)).$this->BsForm->select('Deliberation.service_id', $services, array(
            'label' => 'Service Emetteur',
            'empty' => true,
            'required' => false,
            'escape' => false)).$this->BsForm->select('Deliberation.theme_id', $themes, array(
            'label' => 'Thème',
            'escape' => false,
            'required' => false,
            'default' => $this->Html->value('Deliberation.theme_id'),
            'empty' => true)).$this->BsForm->select('Deliberation.circuit_id', $circuits, array(
            'label' => 'Circuit ',
            'required' => false,
            'default' => $this->Html->value('Deliberation.circuit_id'),
            'empty' => true)).$this->BsForm->select('Deliberation.etat', $etats, array(
            'label' => 'Etat',
            'required' => false,
            'default' => $this->Html->value('Deliberation.etat'),
            'empty' => true));


echo $this->Bs->row();
echo $this->Bs->col('xs'.$this->BsForm->getLeft()).$this->Bs->div('text-right','<strong>'. __('Date projet')).'</strong>'.$this->Bs->close();
$tailleCol=$this->BsForm->getRight()/3;
$this->BsForm->setMax();
echo $this->Bs->col('xs'.$tailleCol);    
echo $this->BsForm->datetimepicker('Deliberation.dateDebut', array(
    'language' => 'fr',
    'autoclose' => 'true',
    'format' => 'yyyy-mm-dd hh:00:00',
    'startView' => 'decade', //decade
    'minView' => 'day',
        ), array(
    'placeholder' => __('Depuis'),
    'label' => false,
    'type' => 'date',
    'style' => 'cursor:pointer',
    'help' => __('Cliquez sur le champs ci-dessus pour choisir la date'),
    'title' => __('Filtre sur les dates des commentaires')));
echo $this->Bs->close();
echo $this->Bs->col('xs'.$tailleCol);   
echo $this->BsForm->datetimepicker('Deliberation.dateFin', array(
    'language' => 'fr',
    'autoclose' => 'true',
    'format' => 'yyyy-mm-dd hh:00:00',
    'startView' => 'decade', //decade
    'minView' => 'day',
        ), array(
    'placeholder' => __('Jusqu\'a'),
    'label' => false,
    'type' => 'date',
    'style' => 'cursor:pointer',
    'help' => __('Cliquez sur le champs ci-dessus pour choisir la date'),
    'title' => __('Filtre sur les dates des commentaires')));
echo $this->Bs->close();
echo $this->Bs->col('xs'.$tailleCol);   
echo $this->BsForm->select('difDate', array(__('1 heure'), __('1 jour'), __('1 mois'), __('1 ans')), array(
    'label' => false,
    'required' => false,
    'default' => $this->Html->value('Deliberation.circuit_id'),
    'empty' => true));

echo $this->Bs->close(2);

$this->BsForm->setDefault();

echo $this->Bs->row();
echo $this->Bs->col('xs'.$this->BsForm->getLeft()).$this->Bs->div('text-right','<strong>'. __('Date préfecture')).'</strong>'.$this->Bs->close();
$tailleCol=$this->BsForm->getRight()/3;

$this->BsForm->setMax();
echo $this->Bs->col('xs'.$tailleCol); 
echo  $this->BsForm->datetimepicker('Deliberation.dateDebutAr', array(
    'language' => 'fr',
    'autoclose' => 'true',
    'format' => 'yyyy-mm-dd hh:00:00',
    'startView' => 'decade', //decade
    'minView' => 'day',
        ), array(
    'placeholder' => __('Depuis'),
    'label' => false,
    'type' => 'date',
    'style' => 'cursor:pointer',
    'help' => __('Cliquez sur le champs ci-dessus pour choisir la date'),
    'title' => __('Filtre sur les dates des commentaires')));
echo $this->Bs->close();
echo $this->Bs->col('xs'.$tailleCol);  
echo $this->BsForm->datetimepicker('Deliberation.dateFinAr', array(
    'language' => 'fr',
    'autoclose' => 'true',
    'format' => 'yyyy-mm-dd hh:00:00',
    'startView' => 'decade', //decade
    'minView' => 'day',
        ), array(
    'placeholder' => __('Jusqu\'a'),
    'label' => false,
    'type' => 'date',
    'style' => 'cursor:pointer',
    'help' => __('Cliquez sur le champs ci-dessus pour choisir la date'),
    'title' => __('Filtre sur les dates des commentaires')));
    echo $this->Bs->close();
echo $this->Bs->col('xs'.$tailleCol);  
echo $this->BsForm->select('difDateAr', array(__('1 heure'), __('1 jour'), __('1 mois'), __('1 ans')), array(
    'label' => false,
    'required' => false,
    'default' => $this->Html->value('Deliberation.circuit_id'),
    'empty' => true));

echo $this->Bs->close(2);
$this->BsForm->setDefault();

//Formulaire informations supplémentaires
foreach ($infosupdefs as $infosupdef) {
    $fieldName = 'Infosup.' . $infosupdef['Infosupdef']['id'];
    if ($infosupdef['Infosupdef']['type'] == 'text' || $infosupdef['Infosupdef']['type'] == 'richText') {
        
        echo $this->BsForm->input($fieldName, array('label' => $infosupdef['Infosupdef']['nom'], 'title' => $infosupdef['Infosupdef']['commentaire']));
    } elseif ($infosupdef['Infosupdef']['type'] == 'date') {
        echo $this->BsForm->input($fieldName, array('label' => $infosupdef['Infosupdef']['nom'], 'size' => '9', 'div' => false, 'title' => $infosupdef['Infosupdef']['commentaire']));
        echo '&nbsp;';
        $fieldId = "'Deliberation.Infosup" . Inflector::camelize($infosupdef['Infosupdef']['id']) . "'";
        echo $this->BsForm->datetimepicker($fieldId, array(
                                            'language' => 'fr',
                                            'autoclose' => 'true',
                                            'format' => 'yyyy-mm-dd hh:00:00',
                                            'startView' => 'decade', //decade
                                            'minView' => 'day',
                                                ), array(
                                            'placeholder' => __('Choisir une date'),
                                            'label' => false,
                                            'type' => 'date',
                                            'style' => 'cursor:pointer',
                                            'help' => __('Cliquez sur le champs ci-dessus pour choisir la date'),
                                            ));
        
    } elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
        echo $this->BsForm->input($fieldName, array('label' => $infosupdef['Infosupdef']['nom'], 'options' => $listeBoolean, 'empty' => true));
        echo $this->BsForm->select($fieldName, $infosuplistedefs[$infosupdef['Infosupdef']['code']], array('label' => $infosupdef['Infosupdef']['nom'], 'empty' => true, 'required' => false,));
    } elseif ($infosupdef['Infosupdef']['type'] == 'listmulti') {
        echo $this->BsForm->select($fieldName, $infosuplistedefs[$infosupdef['Infosupdef']['code']], array('label' => $infosupdef['Infosupdef']['nom'], 'empty' => true, 'required' => false, 'multiple' => true, 'class' => 'select2-infosup'));
    }
}


echo $this->BsForm->select('Deliberation.model', $models, array(
            'after'=> $this->BsForm->checkbox('Deliberation.generer', array('type' => 'checkbox', 'label' => __('Générer le document'), 'div' => false, 'style' => 'float:left; margin-right:15px;')) ,
            'label' => false, 'required' => false, 
            'style' => 'display:; float:left; margin-top:-3px; min-width:220px;', 
            'id' => 'DeliberationModeltemplate'));

echo $this->BsForm->button($this->Bs->icon('search').' '.'Rechercher', array(
    'type' => 'submit', 'div' => false, 
    'class' => 'btn btn-primary col-md-offset-5', 
    'name' => 'Rechercher', 
    'id' => 'submitSearchForm'));
echo $this->Bs->close();
echo $this->BsForm->end();
?>
<script type="application/javascript">
    
    $(document).ready(function () {
    $('select').select2({
    width: "100%",
    allowClear: true,
    placeholder: 'Aucune sélection',
    formatSelection: function (object, container) {
    // trim sur la sélection (affichage en arbre)
    return $.trim(object.text);
    }
    });

    $('#s2id_DeliberationModeltemplate').hide();

    $("#DeliberationGenerer").change(function(){
    if($(this).prop('checked')){
        $('#s2id_DeliberationModeltemplate').show();
        $('#submitSearchForm').html("<i class='fa fa-file-text'></i> Générer le document");
    }
    else {
        $('#s2id_DeliberationModeltemplate').hide();
        $('#submitSearchForm').html("<i class='fa fa-search'></i> Rechercher");
    }
    });

    $('#DeliberationDifDate').on('change', function () {
    var date = new Date(Date.now());
    if ($('#DeliberationDateDebut').val() == '' && $('#DeliberationDateFin').val() == '') {
        $('#DeliberationDateDebut').val(date.getFullYear() + '-' + ajoutZero((date.getMonth()+1).toString()) + '-' + ajoutZero(date.getDate().toString()) + ' ' + ajoutZero(date.getHours().toString()) + ':00:00');
        $('#DeliberationDateFin').val(modifierDate('#DeliberationDifDate',$('#DeliberationDateDebut').val(),1));
    } else if($('#DeliberationDateDebut').val() == '' && $('#DeliberationDateFin').val() != ''){
        $('#DeliberationDateDebut').val(modifierDate('#DeliberationDifDate',$('#DeliberationDateFin').val(),-1));
    }else{
        $('#DeliberationDateFin').val(modifierDate('#DeliberationDifDate',$('#DeliberationDateDebut').val(),1));
    }
    });

    $('#DeliberationDateFin').on('change', function () {
    if ($('#DeliberationDifDate').val() != '') {
        $('#DeliberationDateDebut').val(modifierDate('#DeliberationDifDate',$('#DeliberationDateFin').val(),-1));
    }
    });
    
    $('#DeliberationDateDebut').on('change', function () {
    if ($('#DeliberationDifDate').val() != '') {
        $('#DeliberationDateFin').val(modifierDate('#DeliberationDifDate',$('#DeliberationDateDebut').val(),1));
    }
    });
    
    $('#DeliberationDifDateAr').on('change', function () {
    var date = new Date(Date.now());
    if ($('#DeliberationDateDebutAr').val() == '' && $('#DeliberationDateFinAr').val() == '') {
        $('#DeliberationDateDebutAr').val(date.getFullYear() + '-' + ajoutZero((date.getMonth()+1).toString()) + '-' + ajoutZero(date.getDate().toString()) + ' ' + ajoutZero(date.getHours().toString()) + ':00:00');
        $('#DeliberationDateFinAr').val(modifierDate('#DeliberationDifDateAr',$('#DeliberationDateDebutAr').val(),1));
    } else if($('#DeliberationDateDebutAr').val() == '' && $('#DeliberationDateFinAr').val() != ''){
        $('#DeliberationDateDebutAr').val(modifierDate('#DeliberationDifDateAr',$('#DeliberationDateFinAr').val(),-1));
    }else{
        $('#DeliberationDateFinAr').val(modifierDate('#DeliberationDifDateAr',$('#DeliberationDateDebutAr').val(),1));
    }
    });

    $('#DeliberationDateFinAr').on('change', function () {
    if ($('#DeliberationDifDateAr').val() != '') {
        $('#DeliberationDateDebutAr').val(modifierDate('#DeliberationDifDateAr',$('#DeliberationDateFinAr').val(),-1));
    }
    });
    
    $('#DeliberationDateDebutAr').on('change', function () {
    if ($('#DeliberationDifDateAr').val() != '') {
        $('#DeliberationDateFinAr').val(modifierDate('#DeliberationDifDateAr',$('#DeliberationDateDebutAr').val(),1));
    }
    });
    
    $("#DeliberationGenerer").prop('checked', false);
    }); 
</script>