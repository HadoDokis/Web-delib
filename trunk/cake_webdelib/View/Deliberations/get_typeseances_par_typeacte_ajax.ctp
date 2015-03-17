<?php

if (!empty($typeseances)){
 echo $this->BsForm->select('Deliberation.Seance.Typeseance', $typeseances, array(
                 'options' => $typeseances,
                 'class' => 'select2 selectmultiple',
                 'label' => 'Types de séance',
                 'placeholder'=> __('Choisir un type de séance'),
                 'onchange' => "updateDatesSeances(this);",
                 'multiple' => true)
         );
 echo $this->Bs->scriptBlock('
                 $("#DeliberationSeanceTypeseance").select2({
                     width: "100%",
                     allowClear: true,
                 });
    ');
}