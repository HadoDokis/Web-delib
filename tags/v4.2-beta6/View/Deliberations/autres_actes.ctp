<div class="deliberations">
    <?php if (isset($message)) echo $message; ?>
    <?php
    echo $this->element('filtre');
    echo "<h2>$titreVue</h2>";
    echo $this->Form->create('Deliberation', array('url' => '/deliberations/sendActesToSignature', 'type' => 'file'));
    ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <?php if ($this->action != "autresActesAValider" && !empty($actes)) : ?>
                <th style="width: 2px;"><input type='checkbox' id='masterCheckbox'/></th>
            <?php endif; ?>
            <th>Identifiant</th>
            <th>Type d'acte</th>
            <th>Libellé de l'acte</th>
            <th>Titre</th>
            <th>Classification</th>
            <th>Circuit</th>
            <th>État</th>
            <?php if ($this->action == 'autreActesValides'): ?>
            <th>État parapheur</th>
            <?php endif; ?>
            <th style='width: 65px;'>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $numLigne = 1;
        foreach ($actes as $acte) {
            echo $this->Html->tag('tr', null);
            $numLigne++;

            if ($this->action != "autresActesAValider") {
                $options = array();
                if (empty($acte['Deliberation']['signee'])
                    && (Configure::read('PARAPHEUR') != 'PASTELL' || !empty($acte['Deliberation']['num_pref']))
                    && in_array($acte['Deliberation']['parapheur_etat'], array(null, 0, -1))
                    && $acte['Deliberation']['etat'] >= 2
                )
                    $options['checked'] = true;
                else
                    $options['disabled'] = true;

                echo '<td style="text-align:center;">' . $this->Form->checkbox('Deliberation.id_' . $acte['Deliberation']['id'], $options) . '</td>';
            }

            echo '<td>';
            if (!empty($acte['Deliberation']['num_delib']))
                echo $this->Html->link($acte['Deliberation']['num_delib'], array('action' => 'view', $acte['Deliberation']['id']));
            else
                echo $this->Html->link('Acte : ' . $acte['Deliberation']['id'], array('action' => 'view', $acte['Deliberation']['id']));
            echo '</td>';
            echo '<td>' . $acte['Typeacte']['libelle'] . '</td>';
            echo '<td>' . $acte['Deliberation']['objet'] . '</td>';
            echo '<td>' . $acte['Deliberation']['titre'] . '</td>';
            echo '<td style="text-align:center">' . (!empty($acte['Deliberation']['num_pref']) ? $acte['Deliberation']['num_pref'] . ' - ' . $acte['Deliberation']['num_pref_libelle'] : '<em>-- Manquante --</em>') . '</td>';
            echo '<td>' . $acte['Circuit']['nom'] . "</td>";

            echo '<td>'; // Début de la cellule "Etat"
            switch ($acte['Deliberation']['etat']) {
                case 0:
                    echo "<i class='fa fa-pencil'></i> En cours de rédaction";
                    break;
                case 1:
                    echo "<i class='fa fa-clock-o'></i> Dans un circuit";
                    break;
                case 2:
                    echo "<i class='fa fa-check'></i> Validé";
                    break;
                case 3:
                    echo "<i class='fa fa-thumbs-up'></i> Adopté";
                    break;
                case 4:
                    echo "<i class='fa fa-thumbs-down'></i> Refusé";
                    break;
                case 5:
                    echo "<i class='fa fa-certificate'></i> Envoyé au TDT";
                    break;
            }
            echo '</td>'; // Fin de la cellule "Etat"
            if ($this->action == 'autreActesValides') {
                echo '<td>'; // Debut de la cellule "Etat parapheur"

                switch ($acte['Deliberation']['parapheur_etat']) {
                    case -1 :
                        echo '<i class="fa fa-exclamation-triangle" title="Motif du rejet : ' . $acte['Deliberation']['parapheur_commentaire'] . '"></i>&nbsp;Retour parapheur : refusée';
                        break;
                    case 1 :
                        echo '<i class="fa fa-clock-o"></i> En cours de signature';
                        break;
                    case 2 :
                        echo '<i class="fa fa-check"></i> Approuvé dans le parapheur&nbsp;';
                        if (!empty($acte['Deliberation']['signature']))
                            echo '(Signé&nbsp;<a href="/deliberations/downloadSignature/' . $acte['Deliberation']['id'] . '" title="Télécharger la signature" style="text-decoration: none;"><i class="fa fa-download"></i></a>)';
                        else
                            echo '(Visa)';
                        break;
                    default : //0 ou null
                        if (!empty($acte['Deliberation']['signee']))
                            echo '<i class="fa fa-check"></i> Signature manuscrite';
                        else
                            echo 'Non envoyé';
                }
                echo '</td>'; // Fin de la cellule "Etat parapheur"
            }
            echo $this->Html->tag('td', null, array('style' => 'padding:0'));
            echo $this->Html->link(SHY, '/deliberations/view/' . $acte['Deliberation']['id'], array(
                'class' => 'link_voir',
                'escape' => false,
                'title' => 'voir le projet de ' . $acte['Deliberation']['objet']
            ));
            if ($acte['Deliberation']['etat'] >= 2 && !empty($acte['Deliberation']['signee']))
                $model_id = $acte['Modeltemplate']['modelefinal_id'];
            else
                $model_id = $acte['Modeltemplate']['modeleprojet_id'];
            echo $this->Html->link(SHY, array('controller' => 'models', 'action' => 'generer', $acte['Deliberation']['id'], 'null', $model_id, '-1', '0', 'acte_' . $acte['Deliberation']['id'], '0', '0', '0'), array(
                'class' => 'link_pdf waiter',
                'escape' => false,
                'title' => 'Génération de ' . $acte['Deliberation']['objet']));

            if ($this->action == 'autresActesAValider' && $canGoNext && !empty($acte['Circuit']['nom'])) {
                echo $this->Html->link(SHY, "/deliberations/goNext/" . $acte['Deliberation']['id'], array('class' => "link_jump",
                    'title' => 'Sauter une ou des étapes pour le projet ' . $acte['Deliberation']['objet'],
                    'escape' => false));
            }
            if ($this->action == 'autresActesAValider' && $peuxValiderEnUrgence && !empty($acte['Circuit']['nom'])) {
                echo $this->Html->link(SHY, "/deliberations/validerEnUrgence/" . $acte['Deliberation']['id'], array('class' => "link_validerenurgence",
                    'alt' => 'Valider en urgence le projet ' . $acte['Deliberation']['objet'],
                    'title' => 'Valider en urgence le projet ' . $acte['Deliberation']['objet'],
                    'escape' => false), 'Confirmer la validation en urgence du projet ' . $acte['Deliberation']['id'] . ' ?');
            }

            $enCoursSignature = $acte['Deliberation']['etat'] == 3 && $acte['Deliberation']['parapheur_etat'] == 1;
            if ($this->action == 'autreActesValides' && $canEdit && !$enCoursSignature) {
                echo $this->Html->link(SHY, '/deliberations/edit/' . $acte['Deliberation']['id'], array('class' => 'link_modifier',
                    'title' => 'Modifier le projet ' . $acte['Deliberation']['objet'],
                    'escape' => false
                ));
            }
            if ($this->action == 'autreActesValides' && !$enCoursSignature) {
                $actionAttribuer = '/deliberations/attribuercircuit/' . $acte['Deliberation']['id'];
                echo $this->Html->link(SHY, $actionAttribuer, array('class' => 'link_circuit',
                    'alt' => 'Attribuer un circuit pour le projet ' . $acte['Deliberation']['objet'],
                    'escape' => false,
                    'title' => 'Attribuer un circuit pour le projet ' . $acte['Deliberation']['objet']));
            }
            echo('</td>');
            echo $this->Html->tag('/tr', null);
        }
        ?>
        </tbody>
    </table>
    <br/>
    <?php
    if ($this->action == "autreActesValides" && !empty($actes)) {
        echo '<div id="select-circuit">';
        echo($this->Form->input('Parapheur.circuit_id', array('class' => 'select-circuit select2', 'options' => $circuits, 'label' => array('text' => 'Circuits disponibles', 'class' => 'circuits_label'), 'div' => false)));
        echo $this->Form->button('<i class="fa fa-mail-forward"></i> Envoyer', array('class' => 'btn btn-inverse sans-arrondi', 'escape' => false));
        echo '</div>';
    }
    echo $this->Form->end();
    ?>
</div>

<script type="application/javascript">
    /**
     * Actions au chargement de la page
     */
    $(document).ready(function () {
        $('#ParapheurCircuitId').select2({ width: 'resolve' });
        $('input[type="checkbox"]').change(changeSelection);
        changeSelection();
    });

    /**
     * Afficher/Masquer la sélection de circuit selon si la selection est vide ou non
     */
    function changeSelection() {
        if ($('input[type="checkbox"]:checked').length > 0) {
            $('#select-circuit').show();
        } else {
            $('#select-circuit').hide();
        }
    }
</script>

<style>
    .select2-container .select2-choice {
        border-radius: 0;
    }
</style>
