<?php

if (!empty($typeseances)){
 echo $this->BsForm->input('Typeseance', array(
                 'options' => $typeseances,
                 'id' => 'TypeseanceTypeseance',
                 'name' => 'data[Typeseance][Typeseance]',
                 'type' => 'select',
                 'label' => 'Types de séance',
                 'onchange' => "updateDatesSeances(this);",
                 'multiple' => true));    
echo $this->Bs->scriptBlock('
                 $("#TypeseanceTypeseance").select2({
                     width: "100%",
                     allowClear: true,
                     placeholder: "Selection vide"
                 });
                   ');
}
