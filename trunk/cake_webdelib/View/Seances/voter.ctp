<?php
echo $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min') .
    $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.fr') .
    $this->Html->css('/components/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');
$this->Html->addCrumb('Liste des présents');//, array($this->request['controller'], 'action'=>'index'));
?>

<cake:nocache>
    
<?php 
echo $this->Html->script('vote');

if (isset($message)) 
    echo $this->Bs->div('message', $message, array('id' => 'flashMessage'));


echo $this->Bs->tag('h3', 'Vote pour le projet : '.$deliberation['Deliberation']['objet_delib']);

//Premier panel : Liste des présents
echo $this->Bs->div('panel panel-default') .
    $this->Bs->div('panel-heading', 'Liste des présents') .
    $this->Bs->div('panel-body') .
    $this->Bs->div('btn-group').
    $this->Bs->confirm('Enregistrer la liste des présents', 
    array('controller' => 'deliberations', 'action' => 'listerPresents', $deliberation['Deliberation']['id'], $seance_id), 
    array(
        'type' => 'success', 
        'icon' => 'list', 
        'title' => 'Liste des présents : ' . $deliberation['Deliberation']['objet'],
        'texte' => $this->requestAction(
        array('controller'=>'deliberations', 'action'=>'listerPresents', $deliberation['Deliberation']['id'],$seance_id), array('return')),
            'header' => 'Liste des présents'
        ), 
    array('form'=>true)) .
    $this->Bs->btn('Récupérer la délibération précédente',
        array('controller'=>'deliberations', 
            'action'=>'copyFromPrevious', 
            $deliberation['Deliberation']['id'],
            $seance_id), 
        array(
            'type'=>'primary',
            'icon'=>'cloud-download')).
    $this->Bs->close(3);
    

//Second panel : Liste des présents
$options = array(1 => __('Détail des voix'), 2 => __('Total des voix'), 3 => __('Résultat'), 4 => __('Prendre acte'));
   
echo $this->Bs->div('panel panel-default') .
    $this->Bs->div('panel-heading', 'Vote') .
    $this->Bs->div('panel-body') .
    //creation du formulaire
    $this->BsForm->create('Seances',array('type'=>'post', 'url' => array('controller' => 'seances', 'action' => 'voter', $deliberation['Deliberation']['id'], $seance_id),'novalidate' => true)) .
    $this->BsForm->select('Deliberation.president_id', $acteurs, array(
        'label' => 'Président',
        'div'=>false,
        'class' => 'select2 selectone',
        'empty' => true,
        'autocomplete' => 'off',
        'selected' => $selectedPresident
    )) .
    $this->BsForm->select('Vote.typeVote', $options, array(
        'label'=>'Saisie du vote',
        'div'=>false,
        //'default'=>1,
        'autocomplete'=>'off',
        'value'=>2,
        'class' => 'select2 selectone',
        'empty'=>false)
    );


//TABLEAU VOTE
$this->BsForm->setLeft(0);
$this->BsForm->setRight(12);
echo $this->Bs->row(array('id'=>'voteDetail')).
        $this->Bs->col('xs12');
    //Tableau vote : premiére partie
    $attribute = array();
    $attribute['attributes']['id'] = 'tableDetailVote';
    $attribute['attributes']['name'] = 'tableDetailVote';

    echo $this->Bs->table(
        array(
            array('title' => __('Élus')),
            array('title' => __('Vote'), array('colspan'=> 4)),
        ), 
        array('striped'),
        $attribute
    );

    $this->Bs->setTableNbColumn(4);

    echo $this->Bs->cell('').$this->Bs->cell('Oui').$this->Bs->cell('Non').$this->Bs->cell('Abstention').$this->Bs->cell('Pas de participation');

    foreach ($presents as $present) {

        $this->Bs->lineAttributes(array('class'=>'typeacteur_'.$present['Acteur']['typeacteur_id']));
        
        //Cellule elu
        if($present['Listepresence']['present']==true && empty($present['Listepresence']['suppleant_id']))
            $cell_elu = $present['Acteur']['prenom'].' '.$present['Acteur']['nom'];
        elseif ($present['Listepresence']['present']==true && !empty($present['Listepresence']['suppleant_id']))
            $cell_elu = $present['Listepresence']['suppleant'];
        elseif ($present['Listepresence']['present']==false && !empty($present['Listepresence']['mandataire'])){
            $cell_elu = $present['Acteur']['prenom'].' '.$present['Acteur']['nom'];
            $cell_elu .= ' (donne mandat &agrave; '.$present['Listepresence']['mandataire'].')';
        }
        elseif ($present['Listepresence']['present']==false){
            $cell_elu = $present['Acteur']['prenom'].' '.$present['Acteur']['nom'];
            $cell_elu .= ' (Absent)';
        }
        echo $this->Bs->cell($cell_elu);

        //Cellule vote
        if($present['Listepresence']['present'] == false && empty($present['Listepresence']['mandataire'])) {
            echo $this->Bs->cell('').$this->Bs->cell('').$this->Bs->cell('').$this->Bs->cell('');
        } else {
            $options = array('3', '2', '4', '5');
            foreach ($options as $option)
            {
               // debug($this->request->data['detailVote'][$present['Acteur']['id']].'=>'.$option);
                //debug($this->Html->value('detailVote.'.$present['Acteur']['id']));
               $cell_vote = $this->BsForm->radio('detailVote.'.$present['Acteur']['id'], array($option=>''), array(
                    'fieldset'=>false, 'label'=>false, 'legend'=>false, 'div'=>false,
                   'before'=>null,
                   'after'=>null,
                    //'value'=>$option,
                    'checked'=> (!empty($this->request->data['detailVote'][$present['Acteur']['id']]) && $this->request->data['detailVote'][$present['Acteur']['id']]==$option) ? false : false,
                    'inline'=>true,
                    'onChange' => 'javascript:vote()')); //vote(this)
                echo $this->Bs->cell($cell_vote);
            }
        }
    }

    //Tableau vote : seconde partie
    $this->Bs->setTableNbColumn(4);

    echo $this->Bs->cell('Raccourcis pour les votes').$this->Bs->cell('Oui').$this->Bs->cell('Non').$this->Bs->cell('Abstention').$this->Bs->cell('Pas de participation');

    //Option liste des presents
    echo $this->Bs->cell('Tous les présents');
    $options = array('3', '2', '4', '5');
    foreach ($options as $option)
    {
        $true_false = array($option => '&nbsp;');
        $cell_vote = $this->BsForm->radio(false,$true_false, array(
            'legend' => false,
            'name' => 'racc_tous',
            'inline'=>true,
            'onChange' => 'javascript:vote_global(this, \'tous\');')); //vote(this)

        echo $this->Bs->cell($cell_vote);
    }

    foreach ($typeacteurs as $typeacteur_id => $typeacteur_nom) {
        
        echo $this->Bs->cell($typeacteur_nom);
        $scope = 'typeacteur_'.$typeacteur_id;

        $options = array('3', '2', '4', '5');
        foreach ($options as $option)
        {
            $true_false = array($option => '&nbsp;');
            $cell_vote = $this->BsForm->radio(false,$true_false, array(
                'legend' => false,
                'name' => 'racc_'.$scope,
                'inline'=>true,
                'onChange' => 'javascript:vote_global(this, \''.$scope.'\');')); //vote(this)

            echo $this->Bs->cell($cell_vote);
        }
    }

    $this->Bs->setTableNbColumn(4);

    echo $this->Bs->cell('').$this->Bs->cell('Oui').$this->Bs->cell('Non').$this->Bs->cell('Abstention').$this->Bs->cell('Pas de participation');

    //Resultats
    echo $this->Bs->cell('Total');

    $options = array('3', '2', '4', '5');
    foreach ($options as $option)
    {
        
        echo  $this->Bs->cell(
                $this->Form->input('Vote.res'.$option, array(
                    'disabled' => true,
                    'label'=>false,
                    'autocomplete' => 'off'))
                );
    }

    echo $this->Bs->endTable() .
        $this->BsForm->button('<i class="fa fa-eraser"></i> Remise à zéro des votes', array(
            'type' => 'reset', 
            'div' => false, 
            'onclick' => "$('#tableDetailVote input[type=radio]').removeAttr('checked');",
            'class' => 'btn btn-primary col-md-offset-0', 
            'name' => 'Remise à zéro des votes'
        )). 
$this->Bs->close(2);
    
//SPACER
echo $this->Bs->div('spacer').$this->Bs->close().
$this->Bs->div('spacer').$this->Bs->close().
$this->Bs->div('spacer').$this->Bs->close();

//TABLEAU VOTE TOTAL
echo $this->Bs->div(null, null, array('id' => 'voteTotal'));
    echo $this->Bs->table(
        array(
            array('title' => __('')),
            array('title' => __('Vote'), array('colspan'=>4)),
        ), 
        array('striped'),
        $attribute
    );

    $this->Bs->setTableNbColumn(4);
    echo $this->Bs->cell('').$this->Bs->cell('Oui').$this->Bs->cell('Non').$this->Bs->cell('Abstention').$this->Bs->cell('Pas de participation');
    echo $this->Bs->cell('Nombre total des voix').
        $this->Bs->cell($this->Form->input('Deliberation.vote_nb_oui', array('label'=>false, 'size' => '3'))).
        $this->Bs->cell($this->Form->input('Deliberation.vote_nb_non', array('label'=>false, 'size' => '3'))).
        $this->Bs->cell($this->Form->input('Deliberation.vote_nb_abstention', array('label'=>false, 'size' => '3'))).
        $this->Bs->cell($this->Form->input('Deliberation.vote_nb_retrait', array('label'=>false, 'size' => '3')));
echo $this->Bs->endTable() . $this->Bs->close();

$this->BsForm->setDefault(); 
//TABLEAU VOTE RESULTAT
echo $this->Bs->div(null, null, array('id' => 'voteResultat')) .
    $this->BsForm->radio('Deliberation.etat',array('3'=>'Adopté', '4'=>'Rejeté'), array(
       'label'=>'Vote', 'checked'=>$deliberation['Deliberation']['etat'])) .
$this->Bs->close();

//TABLEAU VOTE PRENDS ACTE
echo $this->Bs->div(null, null, array('id' => 'votePrendsActe')) .
    $this->BsForm->radio('Deliberation.vote_prendre_acte',array('1'=>'Oui', '0'=>'Non'), array(
       'label'=>'Prends acte')) .
$this->Bs->close();

//COMMENTAIRE
$this->BsForm->setLeft(3);
$this->BsForm->setRight(0);
echo $this->Bs->div(null, null, array('id' => 'optional')).
    $this->BsForm->input('Deliberation.vote_commentaire', array('label'=>'Commentaire',
        'style'=>'width:50%; max-width:90%; padding: 5px; max-height: 200px;',
        'type'=>'textarea', 'rows'=>'6', 'cols' => '60', 'maxlength' => '500',
        'after' => '<center><div style="clear:both">(max. <span style="display:inline-block" id="charLeft"></span>/500 caractères)</div></center>')) .
$this->Bs->close(2);

$this->BsForm->setLeft(5);
$this->BsForm->setRight(0);
echo $this->Html2->btnSaveCancel('', $previous, 'Enregistrer le vote');
       
//Fin du formulaire
echo $this->BsForm->end(); 
echo $this->Bs->close(2);
?>

</cake:nocache>