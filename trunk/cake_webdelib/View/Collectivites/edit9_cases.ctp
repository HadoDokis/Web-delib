<?php

echo $this->Bs->tag('h3', 'Modification de l\'affichage des 9 cases');
echo $this->Bs->div('spacer') .  $this->Bs->close();


echo $this->BsForm->create('Collectivites', array(
    'url' => array(
        'controller' => 'collectivites', 
        'action' => 'edit9Cases'), 
    'type' => 'post'));

echo $this->Bs->table(array(array('title' => 'État'),
    array('title' => 'Vue synthétique', array('width'=>'100%')),
    array('title' => 'Actions')), array('hover', 'striped','bordered'));

$this->BsForm->setLeft(0);
$this->BsForm->setRight(12);

$etat=$this->Bs->div('spacer') .  $this->Bs->close().
        $this->Bs->div('spacer') .  $this->Bs->close().
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
);

$etat.= '<h4><span class="label label-default" style="background-color: grey">N°###</span></h4>';


/////////// BOUTTONS
$actions=$this->Bs->div('spacer') .  $this->Bs->close().
        $this->Bs->div('spacer') .  $this->Bs->close().
        $this->Bs->div('btn-group-vertical');
    $actions.= $this->Bs->btn(null,array(), array(
    'type'=>'default',
    'icon'=>'glyphicon glyphicon-eye-open large',
    'disabled'=>'disabled'
));

$actions.= $this->Bs->btn(null,array(), array(
    'type'=>'default',
    'icon'=>'glyphicon glyphicon-edit',
    'disabled'=>'disabled'
));

$actions.=  $this->Bs->btn('',
    array(),
    array('type' => 'default',
        'icon'=>'glyphicon glyphicon-road',
        'escape' => false,
        'disabled'=>'disabled',
    false));

$actions.= $this->Bs->btn(null,array(), array(
    'type'=>'default',
    'icon'=>'glyphicon glyphicon-trash',
    'class' => 'waiter',
    'disabled'=>'disabled'
));

$actions.=$this->Bs->btn('Générer', 
array(), 
array('type' => 'default', 
    'icon' => 'glyphicon glyphicon-cog', 
    'class' => 'waiter',
    'disabled'=>'disabled',
    'title' => 'Générer le document du projet '
    ));

$actions.= $this->Bs->close(1);

$actions.= $this->Bs->close(2);


/*
 * //Creation du tableau a partir du JSON 9cases
$contentFields =  $this->Bs->col('lg4').$this->Bs->panel('');
for($i=1;$i<=9;$i++)
{
    $contentFields .= $this->BsForm->input('Case' . $i, array('value' => $templateProject['Case'.$i], 'label' => false, 'class' => 'caseList'));
    if($i==3 || $i==6 || $i==9)
    {
        $contentFields .= $this->Bs->endPanel().$this->Bs->close();
        if($i!=9) $contentFields .=  $this->Bs->col('lg4').$this->Bs->panel('');
    }
}
$contentFields .= $this->Bs->endPanel();   
 */

$caseList = $this->Bs->div('spacer') .  $this->Bs->close().
            $this->Bs->div('spacer') .  $this->Bs->close().
            $this->Bs->col('lg4').
            $this->Bs->panel('').
            $this->BsForm->input('Case1', array('value' => $templateProject['Case1'], 'label' => false, 'class' => 'caseList')) .
            $this->Bs->endPanel().
            $this->Bs->panel('').
            $this->BsForm->input('Case4', array('value' => $templateProject['Case4'], 'label' => false, 'class' => 'caseList')) .
            $this->Bs->endPanel().
            $this->Bs->panel('').
            $this->BsForm->input('Case7', array('value' => $templateProject['Case7'], 'label' => false, 'class' => 'caseList')) .
            $this->Bs->endPanel().
            $this->Bs->close().
            $this->Bs->col('lg4') .
            $this->Bs->panel('').
            $this->BsForm->input('Case2', array('value' => $templateProject['Case2'], 'label' => false, 'class' => 'caseList')) .
            $this->Bs->endPanel() .
            $this->Bs->panel('').
            $this->BsForm->input('Case5', array('value' => $templateProject['Case5'], 'label' => false, 'class' => 'caseList')) .
            $this->Bs->endPanel() .
            $this->Bs->panel('').
            $this->BsForm->input('Case8', array('value' => $templateProject['Case8'], 'label' => false, 'class' => 'caseList')) .
            $this->Bs->endPanel().
            $this->Bs->close() .
            $this->Bs->col('lg4').
            $this->Bs->panel('').
            $this->BsForm->input('Case3', array('value' => $templateProject['Case3'], 'label' => false, 'class' => 'caseList')) .
            $this->Bs->endPanel() .
            $this->Bs->panel('').
            $this->BsForm->input('Case6', array('value' => $templateProject['Case6'], 'label' => false, 'class' => 'caseList')) .
            $this->Bs->endPanel() .
            $this->Bs->panel('').
            $this->BsForm->input('Case9', array('value' => $templateProject['Case9'], 'label' => false, 'class' => 'caseList')) .
            $this->Bs->endPanel() .
            $this->Bs->close();      
      

    echo $this->Bs->setTableNbColumn(5) .
    $this->Bs->cell($etat) .
    $this->Bs->cell($caseList, 'max') .
    $this->Bs->cell($actions, 'text-right') .

$this->Bs->endTable() .
$this->Bs->btn('Restaurer les valeurs par defaut', array(
    'controller' => 'collectivites',
    'action' => 'edit9Cases',
    'revertModification'), array('type' => 'default',
    'class' => 'waiter',
    'icon' => 'undo',
    'escape' => false,
    'confirm' => 'Etes-vous sur de vouloir restaurer les 9 cases ?',
    'name' => 'Clore',
    'title' => 'Restaurer les valeurs par defaut des 9 cases')) .
$this->Bs->div('text-center') .  
$this->Html2->btnSaveCancel('', $previous, 'Sauvegarder', 'Sauvegarder') .
$this->BsForm->end() .
$this->Bs->close() .
$this->Bs->div('spacer') . $this->Bs->close() .
$this->Bs->div('text-center') .
$this->Bs->close();

        
        
        
        

           

        
        
        
        
      

?>


<script language="javascript">
$(document).ready(function () {
    
    $('#CollectivitesEdit9CasesForm .caseList').select2({
        //multiple: true,
        separator: "&&",
        placeholder: "Selectionnez un élément...",
        data: <?php echo $caseGroup; ?>
    }).on('select2-selecting', function(e) {
        var $select = $(this);
        if (e.val == '') {
            e.preventDefault();
            var childIds = $.map(e.choice.children, function(child) {
                return child.id;
            });
            $select.select2('val', $select.select2('val').concat(childIds));
            $select.select2('close');
        }
    });

   
    $('#CollectivitesEdit9CasesForm').find('.caseList').find("ul.select2-choices").sortable({
        containment: 'parent',
        start: function() { $(this).select2("onSortStart"); },
        update: function() { $(this).select2("onSortEnd"); }
    });


});
</script>