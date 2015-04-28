<?php
/**
 * COLONE ETAT
 */
$etat = $this->Bs->div('spacer') . $this->Bs->close().
        $this->Bs->div('spacer') . $this->Bs->close().
        $this->Bs->btn(null, null, array(
                'tag'=>'button',
                'type'=>'default',
                'class'=>'btn-lg',
                'disabled'=>'disabled',
                'data-toggle'=>'popover',
                'data-placement'=>'right',
                'icon'=> 'glyphicon glyphicon-pause',
                'title' => 'titre'
            )
        ) .
        $this->Bs->div('spacer') . $this->Bs->close().
        $this->Bs->tag('h4', 'N°###', array('class' => 'label label-default', 'style' => 'background-color: grey' ));

/**
 * COLONE ACTION BOUTTONS
 */
$actions=$this->Bs->div('spacer') . $this->Bs->close().
         $this->Bs->div('spacer') . $this->Bs->close().
         $this->Bs->div('btn-group-vertical') .
         $this->Bs->btn(null,array(), array(
            'type'=>'default',
            'icon'=>'glyphicon glyphicon-eye-open large',
            'disabled'=>'disabled'
         )) .
         $this->Bs->btn(null,array(), array(
            'type'=>'default',
            'icon'=>'glyphicon glyphicon-edit',
            'disabled'=>'disabled'
         )) .
         $this->Bs->btn('',
            array(),
            array('type' => 'default',
                'icon'=>'glyphicon glyphicon-road',
                'escape' => false,
                'disabled'=>'disabled',
            false)) .
         $this->Bs->btn(null,array(), array(
            'type'=>'default',
            'icon'=>'glyphicon glyphicon-trash',
            'class' => 'waiter',
            'disabled'=>'disabled'
         )) .
         $this->Bs->btn('Générer', 
         array(), 
         array('type' => 'default', 
            'icon' => 'glyphicon glyphicon-cog', 
            'class' => 'waiter',
            'disabled'=>'disabled',
            'title' => 'Générer le document du projet '
            )) .
         $this->Bs->close(3);

/**
 * COLONE VUE SYNTHETIQUE : Creation du tableau a partir du JSON 9cases
 */
$this->BsForm->setLeft(0);
$this->BsForm->setRight(12);
$caseList = $this->Bs->div('spacer') .  $this->Bs->close().
                 $this->Bs->div('spacer') .  $this->Bs->close(). 
                 $this->Bs->col('lg4').$this->Bs->panel('');
foreach($templateProject as $key=>$case)
{
    $caseList .= 
    $this->BsForm->select('Case'.$key, $selecteurProject, array(
           'label' => false,
           'class' => 'select2 selectone',
           'default' => $case,
           'inline' => true,
           'autocomplete' => 'off',
           'escape' => false));
    if($key==2 || $key==5 || $key==8)
    {
        $caseList .= $this->Bs->endPanel().$this->Bs->close();
        if($key!=8) $caseList .=  $this->Bs->col('lg4').$this->Bs->panel('');
    }
}
$caseList .= $this->Bs->close(3);

                  
/**
 * AFFICHAGE
 */
$this->Html->addCrumb('Modification de l\'affichage des 9 cases');
echo $this->Bs->tag('h3', 'Modification de l\'affichage des 9 cases') .
     $this->Bs->div('spacer') .  $this->Bs->close() .
     $this->BsForm->create('Collectivites', array(
                    'url' => array(
                        'controller' => 'collectivites', 
                        'action' => 'edit9Cases'), 
                    'type' => 'post')) .
     $this->Bs->table(array(
         array('title' => 'État'),
         array('title' => 'Vue synthétique', array('width'=>'100%')),
         array('title' => 'Actions')), array('hover', 'striped','bordered')) .
     $this->Bs->setTableNbColumn(5) .
     $this->Bs->cell($etat) .
     $this->Bs->cell($caseList, 'max') .
     $this->Bs->cell($actions, 'text-right') .
     $this->Bs->endTable() .
     $this->Bs->btn('Restaurer les valeurs par defaut', 
        array(
            'controller' => 'collectivites',
            'action' => 'edit9Cases',
            'revertModification'), 
        array('type' => 'default',
            'class' => 'waiter',
            'icon' => 'undo',
            'escape' => false,
            'name' => 'Clore',
            'title' => 'Restaurer les valeurs par defaut des 9 cases'),
        'Etes-vous sur de vouloir restaurer les 9 cases ?'
        ) .
     $this->Bs->div('text-center') .  
     $this->Html2->btnSaveCancel('', $previous, 'Sauvegarder', 'Sauvegarder') .
     $this->BsForm->end() .
     $this->Bs->close() .
     $this->Bs->div('spacer') . $this->Bs->close() .
     $this->Bs->div('text-center') .
     $this->Bs->close();