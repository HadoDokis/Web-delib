<div class="deliberations">
    <?php if (isset($message)) echo $message; ?>
    <?php
    echo $this->element('filtre');
    echo "<h2>$titreVue</h2>";
    echo $this->Form->create('Deliberation', array('url' => '/deliberations/sendActesToSignature', 'type' => 'file'));
    ?>
    <table style="width: 100%;">
        <tr>
            <th></th>
            <th>Identifiant</th>
            <th>Type d'acte</th>
            <th>Libellé de l'acte</th>
            <th>Titre</th>
            <th>Circuit</th>
            <th>État</th>
            <th style='width: 65px;'>Action</th>
        </tr>

        <?php
        $numLigne = 1;
        foreach ($actes as $acte) {
            $rowClass = ($numLigne & 1) ? array('style'=> 'height: 38px') : array('style'=> 'height: 38px', 'class' => 'altrow');
            echo $this->Html->tag('tr', null, $rowClass);
            $numLigne++;
            if ( $this->action != "autresActesAValider"
                && $acte['Deliberation']['signee'] != 1
                && $acte['Deliberation']['etat'] >= 2 && $acte['Deliberation']['etat'] < 5
                && $acte['Deliberation']['etat_parapheur'] <= 0 )
                echo '<td>' . $this->Form->checkbox('Deliberation.id_' . $acte['Deliberation']['id'], array('checked' => true, 'autocomplete' => 'off')) . '</td>';
            else
                echo '<td></td>';

            echo '<td style="width: 20px;">' . $acte['Deliberation']['id'] . '</td>';
            echo '<td>' . $acte['Typeacte']['libelle'] . '</td>';
            echo '<td>' . $acte['Deliberation']['objet'] . '</td>';
            echo '<td>' . $acte['Deliberation']['titre'] . '</td>';
            echo '<td>' . $acte['Circuit']['nom'] . "</td>";

            echo '<td>'; // Début de la cellule "Etat"
            switch ($acte['Deliberation']['etat']) {
                case 0:
                    echo "En cours de rédaction";
                    break;
                case 1:
                    echo "Dans un circuit";
                    break;
                case 2:
                    echo "Validé";
                    break;
                case 3:
                    echo "Voté et adopté";
                    break;
                case 4:
                    echo "Voté et refusé";
                    break;
                case 5:
                    echo "Voté et envoyé";
                    break;
            }
            $enCoursSignature = ($acte['Deliberation']['etat'] == 3 && $acte['Deliberation']['etat_parapheur'] == 1);
            if ($acte['Deliberation']['signee'] == 1) {
                echo ' / Signé';
            } elseif ($acte['Deliberation']['etat_parapheur'] == -1) {
                echo ' / Signature refusée';
            } elseif ($enCoursSignature) {
                echo ' / En cours de signature';
            }
            echo '</td>'; // Fin de la cellule "Etat"

            echo ('<td>');
            echo $this->Html->link(SHY, '/deliberations/view/' . $acte['Deliberation']['id'], array(
                    'class' => 'link_voir',
                    'escape' => false,
                    'title' => 'voir le projet de ' . $acte['Deliberation']['objet']
                ));
            if (($acte['Deliberation']['etat'] >= 2) && ($acte['Deliberation']['signee'] == 1))
                $model_id = $acte['Model']['modelefinal_id'];
            else
                $model_id = $acte['Model']['modeleprojet_id'];

            echo $this->Html->link(SHY, '/models/generer/' . $acte['Deliberation']['id'] . '/null/' . $model_id, array('class' => 'link_pdf',
                'escape' => false,
                'title' => 'Génération de ' . $acte['Deliberation']['objet']));

            if ($this->action == 'autresActesAValider' && $canGoNext && !empty($acte['Circuit']['nom'])) {
                echo $this->Html->link(SHY, "/deliberations/goNext/" . $acte['Deliberation']['id'], array('class' => "link_jump",
                    'title' => 'Sauter une ou des étapes pour le projet ' . $acte['Deliberation']['objet'],
                    'escape' => false), false);
            }
            if ($this->action == 'autresActesAValider' && $peuxValiderEnUrgence && !empty($acte['Circuit']['nom'])) {
                echo $this->Html->link(SHY, "/deliberations/validerEnUrgence/" . $acte['Deliberation']['id'], array('class' => "link_validerenurgence",
                    'alt' => 'Valider en urgence le projet ' . $acte['Deliberation']['objet'],
                    'title' => 'Valider en urgence le projet ' . $acte['Deliberation']['objet'],
                    'escape' => false), 'Confirmez-vous la validation en urgence du projet \'' . $acte['Deliberation']['id'] . '\'');
            }
            if ($this->action == 'autreActesValides' && $canEdit && !$enCoursSignature) {
                echo $this->Html->link(SHY, '/deliberations/edit/' . $acte['Deliberation']['id'], array('class' => 'link_modifier',
                    'title' => 'Modifier le projet ' . $acte['Deliberation']['objet'],
                    'escape' => false
                        ), false);
            }
            if ($this->action == 'autreActesValides' && !$enCoursSignature) {
                $actionAttribuer = '/deliberations/attribuercircuit/' . $acte['Deliberation']['id'];
                echo $this->Html->link(SHY, $actionAttribuer, array('class' => 'link_circuit',
                    'alt' => 'Attribuer un circuit pour le projet ' . $acte['Deliberation']['objet'],
                    'escape' => false,
                    'title' => 'Attribuer un circuit pour le projet ' . $acte['Deliberation']['objet']), false);
            }
            echo ('</td>');
            echo $this->Html->tag('/tr', null);
        }
        ?>
    </table>
    <br />
        <?php
        if ($this->action == "autreActesValides") {
            echo 'Circuits disponibles : ';
            echo $this->Form->input('Parapheur.circuit_id', array('options' => $circuits, 'label' => false, 'div' => false)) . '<br /><br />';
            echo '<div class="submit">';
        }
        if ($this->action != 'autresActesAValider')
            echo $this->Form->button('<i class="icon-cloud-upload"></i> Envoyer', array('class' => 'btn btn-primary', 'escape' => false));
        echo $this->Form->end();
        echo '</div>';
        ?>
</div>
