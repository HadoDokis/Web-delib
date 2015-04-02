<?php 
$this->Html->addCrumb('Liste des compteurs', array('action'=>'index'));

$aideformatOptions =  array(
        'AIDEFORMAT' => 'Sélectionner les formats dans la liste', 
        '#s#'=> 'numéro de la séquence',
        '#S#'=> 'numéro de la séquence sur 1 chiffre',
        '#SS#'=> 'numéro de la séquence sur 2 chiffres (complété par un souligné)',
        '#SSS#'=> 'numéro de la séquence sur 3 chiffres (complété par des soulignés)',
        '#SSSS#'=> 'numéro de la séquence sur 4 chiffres (complété par des soulignés)',
        '#00#'=> 'numéro de la séquence sur 2 chiffres (complété par un zéro)',
        '#000#'=> 'numéro de la séquence sur 3 chiffres (complété par des zéros)',
        '#0000#'=> 'numéro de la séquence sur 4 chiffres (complété par des zéros)',
        '#AAAA#'=> 'année sur 4 chiffres',
        '#AA#'=> 'année sur 2 chiffres',
        '#M#'=> 'numéro du mois sans zéro significatif',
        '#MM#'=> 'numéro du mois avec zéro significatif',
        '#J#'=> 'numéro du jour sans zéro significatif',
        '#JJ#'=> 'numéro du jour avec zéro significatif',
        '#p#'=> 'numéro de la position');
$aideformatDateOptions =  array(
        'AIDEFORMAT' => 'Sélectionner les formats dans la liste',
        '#AAAA#' => 'Année',
        '#MM#'=> 'Mois',
        '#JJ#' => 'Jour');

echo $this->Html->script('compteurs.js');
if ($this->Html->value('Compteur.id')) {
    $this->Html->addCrumb('Modification d\'un compteur');
    echo $this->Bs->tag('h3', 'Modification d\'un compteur') .
    $this->BsForm->create('Compteur', array(
         'url' => array(
             'admin' => 'true',
             'prefix' => 'admin',
             'controller' => 'compteurs', 
             'action' => 'edit',
             $this->Html->value('Compteur.id')
             ), 
        'type' => 'post'));
} else {
    $this->Html->addCrumb('Création d\'un compteur');
    echo $this->Bs->tag('h3', 'Création d\'un compteur') .
    $this->BsForm->create('Compteur', array(
         'url' => array(
             'admin' => 'true',
             'prefix' => 'admin',
             'controller' => 'compteurs', 
             'action' => 'add'
             ), 
        'type' => 'post'));
}

echo $this->Bs->div('required') .
     $this->BsForm->input('Compteur.nom', array(
         'label' => 'Nom <acronym title="obligatoire">(*)</acronym>')) .
     $this->Bs->close() . 
     $this->Bs->div('spacer').$this->Bs->close() . 
     $this->Bs->div('required') .
     $this->BsForm->input('Compteur.commentaire', array(
         'label' => 'Commentaire')) .       
     $this->Bs->close() . 
     $this->Bs->div('spacer').$this->Bs->close() . 
     $this->Bs->div('required') .
     $this->BsForm->input('Compteur.def_compteur', array(
         'label' => 'Définition du compteur <acronym title="obligatoire">(*)</acronym>', 
         'maxlength' => '45')) .
     $this->Bs->close() . 
     $this->Bs->div('spacer').$this->Bs->close() . 
     $this->Bs->div('form-group') .
        $this->Bs->col('md3').
        $this->Bs->close() . 
        $this->Bs->col('md9').
        $this->BsForm->select('', $aideformatOptions, array(
           'label' => false,
           //'class' => 'select2 selectone',
           'selected' => 'AIDEFORMAT',
           'inline' => true,
           'autocomplete' => 'off',
           'onChange' => "InsertSelectedValueInToInput(this, 'CompteurDefCompteur');",
           'escape' => false)) .
     $this->Bs->close(2) . 
     $this->Bs->div('spacer').$this->Bs->close() . 
     $this->Bs->div('required') .
     $this->BsForm->input('Compteur.def_reinit', array(
                         'label' => 'Critère de réinitialisation')) .
     $this->Bs->close() . 
     $this->Bs->div('spacer').$this->Bs->close() . 
     $this->Bs->div('form-group') .
        $this->Bs->col('md3').
        $this->Bs->close() . 
        $this->Bs->col('md9').
        $this->BsForm->select('', $aideformatDateOptions, array(
           'label' => false,
          // 'class' => 'select2 selectone',
           'selected' => 'AIDEFORMAT',
           'inline' => true,
           'autocomplete' => 'off',
           'onChange' => "InsertSelectedValueInToInput(this, 'CompteurDefReinit');",
           'escape' => false)) .
     $this->Bs->close(2) . 
     $this->Bs->div('spacer').$this->Bs->close() . 
     $this->Bs->div('required') .
     $this->BsForm->select('Compteur.sequence_id', $sequences, array(
                        'label' => 'Séquence <acronym title="obligatoire">(*)</acronym>', 
                        'empty' => (count($sequences) > 1))) .
     $this->Bs->close() .
     $this->Form->hidden('Compteur.id') .
     $this->Html2->btnSaveCancel('', $previous, 'Enregistrer', 'Enregistrer') .
     $this->BsForm->end();

