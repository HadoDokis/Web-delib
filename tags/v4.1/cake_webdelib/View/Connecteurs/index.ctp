<h2> Gestion des connecteurs </h2>
<table class="table table-hover" >
    <th> Types </th>
    <th width='100px'> Actions </th>
<?php
    $numLigne = 1;
    foreach ($connecteurs as $id => $connecteur) {
        if ($id == -1) continue; 
        $rowClass = ($numLigne & 1)?array('height' => '36px'):array('height' => '36px', 'class'=>'altrow');
        echo $this->Html->tag('tr', null, $rowClass);
        echo '<td>'. $connecteur.'</td>';
        echo '<td>'.$this->Html->link(SHY,
                                         "/connecteurs/edit/$id",
                                         array('class'=>'link_modifier',
                                               'alt'=>"Modifier le connecteur $connecteur",
                                               'title'=>"Modifier le connecteur $connecteur",
                                               'escape' => false
                                               ));
        if ($id != 8) {
            echo $this->Html->link(SHY,
                                   "/check2/index2.php#".strtolower($connecteur),
                                   array('class'=>'link_validerenurgence',
                                         'alt'=>"Tester le connecteur $connecteur",
                                         'target'=>"_blank",
                                         'title'=>"Tester le connecteur $connecteur",
                                          'escape' => false
                                         )).'</td>';
        }
        echo '</tr>';
    }

?>
</table>
