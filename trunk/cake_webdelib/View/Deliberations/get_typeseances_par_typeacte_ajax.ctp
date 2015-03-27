<?php

if (!empty($typeseances)){
 echo $this->BsForm->select('Typeseance', $typeseances, array(
                 'class' => 'select2 selectmultiple',
                 'label' => 'Types de séance',
                 'placeholder'=> __('Choisir un type de séance'),
                 'autocomplete' => 'off',
                 'onchange' => "updateDatesSeances(this);",
                 'multiple' => true)
         );
 echo $this->Bs->scriptBlock('
                 $("#Typeseance").select2({
                     width: "100%",
                     allowClear: true,
                 });
    ');
}