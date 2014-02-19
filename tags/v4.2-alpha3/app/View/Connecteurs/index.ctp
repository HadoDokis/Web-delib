<?php
echo $this->Html->css('connecteurs');
?>
<h2>Gestion des connecteurs</h2>
<table class="table table-hover" id="connecteurs">
    <thead>
    <th>Types</th>
    <th>Actions</th>
    </thead>
    <tbody>
        <?php
        $numLigne = 1;
        foreach ($connecteurs as $id => $connecteur) {
            if ($id < 1)
                continue;
            $rowClass = ($numLigne & 1) ? array('style' => 'height: 36px') : array('style' => 'height: 36px', 'class' => 'altrow');
            echo $this->Html->tag('tr', null, $rowClass);
            echo '<td>' . $connecteur . '</td>';
            echo '<td class="actions">';
            echo $this->Html->link(SHY, "/connecteurs/edit/$id", array('class' => 'link_modifier',
                'title' => "Modifier le connecteur $connecteur",
                'escape' => false
            ));
            if ($id != 8) {
                echo $this->Html->link(SHY, "/check/index.php#" . urlencode(strtolower($connecteur)), array('class' => 'link_validerenurgence',
                    'target' => "_blank",
                    'title' => "Tester le connecteur $connecteur",
                    'escape' => false
                ));
            }
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>