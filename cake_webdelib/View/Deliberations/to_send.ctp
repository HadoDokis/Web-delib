<?php 

if ($this->action == 'autreActesAEnvoyer') {
    echo $this->Bs->tag('h3', 'Télétransmission des actes');
} elseif ($this->action == 'toSend') {
    echo $this->Bs->tag('h3', 'Télétransmission des délibérations');
}

echo $this->Html->script('utils.js');
    if ((@$this->params['filtre'] != 'hide') &&
        ($this->params['action'] != 'mesProjetsRecherche') &&
        ($this->params['action'] != 'tousLesProjetsRecherche')
    )
        echo $this->element('filtre');

    


$classification='';
if (!empty($dateClassification)) {
    $classification = __('La Classification enregistrée date du') . ' ' . $dateClassification;
$this->Bs->div();
} else {
    $classification = $this->Bs->icon('warning') . __('Classification non téléchargée');
}


echo $this->Bs->div('alert alert-info', $classification. ' ' .
        $this->Html->link('<i class="fa fa-refresh"></i>', array('controller'=>'deliberations', 'action' => 'getClassification'), array(
    'title' => 'Télécharger/Mettre à jour les données de classification', 
    'escape' => false)
        )
        ).
$this->Bs->div();

echo $this->BsForm->create('Deliberation', array('type' => 'file', 'url' => array('controller' => 'deliberations', 'action' => 'sendToTdt'), 'class' => 'waiter'));

echo $this->Bs->table(array(
    array('title' => $this->BsForm->checkbox(null, array(
        'id'=>'masterCheckbox',
        'inline'=>'inline',
        'autocomplete'=>'off'
        ))),
    array('title' => 'id'),
    array('title' => 'Numéro Délibération'),
    array('title' => 'Libellé de l\'acte'),
    array('title' => 'Titre'), 
    array('title' => 'Annexe(s)'), 
    array('title' => 'Classification'),
    array('title' => 'Statut'),
        ), array('hover', 'striped'));

foreach ($deliberations as $deliberation) {
    
    $options = array('hiddenField' => false);
    if ($deliberation['Deliberation']['etat'] < 5)
        $options['checked'] = true;
    else
        $options['disabled'] = true;
   
    echo $this->Bs->cell($this->BsForm->checkbox('Deliberation.' . $deliberation['Deliberation']['id'] .'.send', array_merge($options, array(
       'label'=> false,
       'autocomplete'=>'off',
       'inline'=>'inline',
   ))));
   echo $this->Bs->cell($this->Html->link($deliberation['Deliberation']['id'], array(
       'controller'=>'deliberations',
       'action' => 'view', $deliberation['Deliberation']['id'])));
   
   echo $this->Bs->cell($this->Html->link($deliberation['Deliberation']['num_delib'], array(
            'controller'=>'deliberations',
            'action'=>'downloadDelib', $deliberation['Deliberation']['id'])));
        echo $this->Bs->cell($deliberation['Deliberation']['objet_delib']);
        echo $this->Bs->cell($deliberation['Deliberation']['titre']);
        
        
        $id_num_pref = $deliberation['Deliberation']['id'] . '.num_pref';
        if ($deliberation['Deliberation']['etat'] == 5){
             echo $deliberation['Deliberation']['num_pref'] . ' - ' . $deliberation['Deliberation']['num_pref_libelle'] ;
        }
        else {
            if (!empty($nomenclatures)) {
                
                $select=$this->Form->input($id_num_pref, array(
                    'name' => $deliberation['Deliberation']['id'] . 'classif2',
                    'label' => false,
                    'options' => $nomenclatures,
                    'default' => $deliberation['Deliberation']['num_pref'],
                    'readonly' => empty($nomenclatures),
                    'empty' => true,
                    'class' => 'selectone',
                    'style' => 'width:auto; max-width:500px;',
                    'div' => array('style' => 'text-align:center;font-size: 1.1em;'),
                    'escape' => false
                ));
            } else {
                $this->BsForm->setLeft(0);
                $this->BsForm->setRight(0);
                $select=$this->BsForm->select($id_num_pref, $optionsNumPref , array(
                    'class'=>'selectone',
                    'default'=>!empty($deliberation['Deliberation']['num_pref'])?$deliberation['Deliberation']['num_pref']:null,
                    'placeholder' => 'Cliquer ici pour choisir la classification',
                ));
                
                /*$select=$this->BsForm->inputGroup('Deliberation.' . $deliberation['Deliberation']['id'] . '_num_pref_libelle', array(
                            'content'=>'<i class="fa fa-eraser"></i>',
                            'type' => 'button',
                            'id'=>'deselectClassif',
                            'state' => 'primary',
                            'side'=>'right'), array(
                                'placeholder' => 'Cliquer ici pour choisir la classification',
                                'onclick' => "javascript:window.open('" . Router::url(array('controller' => 'deliberations', 'action' => 'classification')) . "', '".'Deliberation.' . $deliberation['Deliberation']['id'] . '_num_pref_libelle'."', 'scrollbars=yes,width=570,height=450');",
                                'id' => 'classif1',
                                'title' => 'Selection de la classification',
                                'readonly' => 'readonly',
                                'class'=>'pull-left'));
        
                $select.=$this->Form->hidden('Deliberation.' . $deliberation['Deliberation']['id'] . '_num_pref', array(
                    'id' => $deliberation['Deliberation']['id'] . 'classif2',
                    'name' => $deliberation['Deliberation']['id'] . 'classif2',
                    'value' => $deliberation['Deliberation']['num_pref']
                ));*/
            }
        }
        $annexes='';
        if(!empty($deliberation['Annex'])){
        //$annexes=$this->Bs->link('Annexes'.'<span class="badge">'.count($deliberation['Annex']).'</span>', array(), array('escape'=>false));
        $annexes='<div class="dropdown">
            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
              Liste des annexes <span class="label label-info label-as-badge">'.count($deliberation['Annex']).'</span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">';
                  foreach ($deliberation['Annex'] as $annexe) {
                  $annexes.='<li role="presentation">' .
                  $this->Bs->btn($annexe['filename_pdf'], array('controller' => 'annexes', 'action' => 'download', $annexe['id']), array('icon' => 'glyphicon glyphicon-download-alt',
                      'role' => 'menuitem',
                      'tabindex' => -1
                  ))
                  . '</li>';
              }

              echo '</ul>
          </div>';
        }
        echo $this->Bs->cell($annexes);
        
        echo $this->Bs->cell($select);
        
        if ($deliberation['Deliberation']['etat'] == 5) {
            $statut = "<i class='fa fa-check-circle'></i> Envoyé";
        } else {
            $statut = "Non envoyé";
        }
        echo $this->Bs->cell($statut);
    }
echo $this->Bs->endTable();

if (!empty($deliberations)) {
    echo $this->Form->button('<i class="fa fa-cloud-upload"></i> Envoyer', array('escape' => false, 'type' => 'submit', 'class' => 'btn btn-primary'));
}
if (isset($seance_id)) {
    echo $this->Form->hidden('Seance.id', array('value' => $seance_id));
}

echo $this->BsForm->end();