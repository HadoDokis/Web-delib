<?php

$this->Html->addCrumb('Attribuer un circuit');
echo $this->Bs->tag('h3', 'Attribuer un circuit au projet: ' . $projet['Deliberation']['objet'].' <span class="label label-info">'.$projet['Deliberation']['id'].'</span>');

echo $this->element('projetInfo', array('projet' => $projet));

echo $this->Html->tag('br /');

echo $this->BsForm->create('Deliberation', array('type' => 'post', 'url' => array('plugin' => null, 'controller' => 'deliberations', 'action' => 'attribuercircuit', $projet['Deliberation']['id'])));

echo $this->BsForm->select('Deliberation.circuit_id', $circuits, array(
    'type' => 'select',
    'label' => 'Choisir un circuit',
    'class' => 'select2 selectone',
    'autocomplete'=>'off',
    'default'=> isset($userCircuitDefaultId) ? $userCircuitDefaultId : false,
    'data-placeholder' => 'Sélectionnez un circuit',
    'empty' => ''
    ));

// données concernant le circuit selectionné
echo $this->Bs->row() .
 $this->Bs->col('xs3')
 . $this->Bs->close() .
 $this->Bs->col('xs9') . $this->Bs->div(null, (isset($visu) ? $visu : ''), array('id' => 'selectCircuit'))
 . $this->Bs->close(2);

echo $this->Bs->div('btn-group', null) .
 $this->Bs->btn('Annuler', $previous, array(
    'type' => 'default',
    'icon' => 'glyphicon glyphicon-arrow-left',
    'escape' => false,
    'title' => 'Annuler les modifications')) .
 $this->Bs->btn('Modifier projet', array('controller' => 'deliberations', 'action' => 'edit', $projet['Deliberation']['id']), array(
    'type' => 'primary',
    'icon' => 'glyphicon glyphicon-edit',
    'escape' => false,
    'title' => 'Modifier le projet')) .
 $this->Bs->btn('Insérer le projet dans le circuit', null, array(
    'tag' => 'button',
    'type' => 'success',
    'id' => 'attribuer',
    'name' => 'attribuer',
    'icon' => 'glyphicon glyphicon-road',
    'escape' => false,
    'title' => 'Insérer le projet dans le circuit')) .
 $this->Bs->close();

echo $this->BsForm->end();

echo $this->Html->scriptBlock('
    $(document).ready(function(){
       if ($(".parapheur_error").length > 0){
           $("#attribuer").remove();
       }
    });
    
    $("#DeliberationCircuitId").select2({
        allowClear: true
    }).on("select2-removed", 
    function(e) { 
        $("#selectCircuit").html(\'\');
    }).on("change", 
    
    function(e) {
    
        if($("#DeliberationCircuitId").val() != \'\'){
            var ajaxUrl = \'' . $this->Html->url(array(
            'controller' => 'cakeflow',
            'action' => 'circuits', 'visuCircuit')) . '/\' + $("#DeliberationCircuitId").val();
            $.ajax({
                url: ajaxUrl,
                beforeSend: function () {
                    $("#selectCircuit").html(\'\');
                },
                success: function (result) {
                    $("#selectCircuit").html(result);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert(textStatus);
                }
            });
        }
    });

');
