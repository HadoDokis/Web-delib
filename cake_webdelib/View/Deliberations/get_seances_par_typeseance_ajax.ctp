<?php

if (!empty($seances)){
    echo $this->Form->input('Seance', array(
                        'options' => $seances,
                        'id' => 'SeanceSeance',
                        'name' => 'data[Seance][Seance]',
                        'type' => 'select',
                        'label' => 'Dates de séance',
                        'multiple' => true));
    echo $this->Bs->scriptBlock('
                        $("#SeanceSeance").select2({
                            width: "80%",
                            allowClear: true,
                            placeholder: "Selection vide"
                        });
                          ');
 }