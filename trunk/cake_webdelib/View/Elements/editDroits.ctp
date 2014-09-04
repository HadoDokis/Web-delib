<?php
/*
	Affiche les menus-controlleurs pour la saisie des droits
	Paramètres :
*/
?>
<tr>
    <td><em><label for="masterCheckboxDroits" style="width: auto; font-size: 11px;">Tout cocher / décocher</label></em></td>
    <td><input type="checkbox" id="masterCheckboxDroits" /></td>
</tr>
<div class="panel-group" id="accordion">
<?php

//debug($listeCtrlAction);
$this->BsForm->setLeft(0);
foreach($listeCtrlAction as $key => $ctrlAction) {
    if ($ctrlAction['niveau']==0){
    ?>  
    <div class="panel panel-default">

      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#droit_collapse<?php echo $key; ?>">
            <?php 
            echo $this->BsForm->checkbox('Droits.'.$ctrlAction['acosAlias'], 
                    array('id' => 'chkBoxDroits'.$key,'label'=>$ctrlAction['title'])
                    );
             ?>
          </a>
        </h4>
      </div><?php
        if ($ctrlAction['nbSousElements']>0) {
        $debut=0;
        $fin=$ctrlAction['nbSousElements']; 
        ?>
         <div id="droit_collapse<?php echo $key; ?>" class="panel-collapse collapse in">
        <div class="panel-body">
    <?php }else { ?>
            </div>
    <?php
        }
    }   
            if ($ctrlAction['niveau']==1){
                //modifiable
                echo $this->BsForm->checkbox('Droits.'.$ctrlAction['acosAlias'], 
                        array('id' => 'chkBoxDroits'.$key,'label'=>$ctrlAction['title'])
                );
                $debut++;
            }
    if ($ctrlAction['niveau']==1 && $debut==$fin){
          ?>
      </div>
    </div>
</div>
    <?php } ?>
     
<?php } ?>
</div>
<?php
return;

foreach($listeCtrlAction as $rownum => $ctrlAction) {
	$classTd = 'niveau'.$ctrlAction['niveau'];
    $icon = '';
    if ($ctrlAction['niveau']==0)
        $icon = '<i class="fa fa-angle-down"></i>&nbsp;&nbsp;';
    if ($ctrlAction['niveau']==1)
        $icon = '<i class="fa fa-angle-right"></i>&nbsp;&nbsp;';

    $ctrlAction['title']='<label for="chkBoxDroits'.$rownum.'">'.$icon.$ctrlAction['title'].'</label>';

	$optionsCheckBox = array(
		'label' => false,
		'type' => 'checkbox',
		'id' => 'chkBoxDroits'.$rownum
    );

	if ($ctrlAction['nbSousElements']>0)
		$optionsCheckBox['onclick'] = 'toggleCheckBoxDroits('.$rownum.', '.$ctrlAction['nbSousElements'].');';

	echo $this->Html->tag('tr');
		echo $this->Html->tag('td', $ctrlAction['title'], array('class'=>$classTd.' td-droit'));
		
	echo $this->Html->tag('/tr');
}
echo $this->Html->tag('/table');