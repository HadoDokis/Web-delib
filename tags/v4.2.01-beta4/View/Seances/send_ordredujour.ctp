<div class="deliberations">
    <h2>Envoi de l'ordre du jour</h2>
    <?php
    echo $this->Form->create('Seance', array('url' => array('controller' => 'seances', 'action' => 'sendOrdredujour', $seance_id, $model_id), 'class'=>'waiter', 'data-modal' => 'Envoi de l\'ordre du jour'));

    echo $this->Html->tag('div', null, array('id' => 'boutons_generation_odj'));

    echo $this->Html->link("<i class='fa fa-cogs'></i> Générer l'ordre du jour",
        array('controller' => 'seances', 'action' => 'genereFusionToFiles', $seance_id, $model_id, 'ordredujour'),
        array('class' => "btn btn-success waiter", 'escape' => false, 'title' => 'Générer l\'ordre du jour', 'data-modal' => 'Génération de l\'ordre du jour en cours', 'style' => 'margin-right:15px;'));

    echo $this->Html->tag('i', '', array('class'=> 'fa fa-arrow-right'));

    echo $this->Html->link("<i class='fa fa-download'></i> Télécharger une archive contenant tous les ODJ",
        array('controller' => 'seances', 'action' => 'downloadZip', $seance_id, $model_id),
        array('class' => "btn btn-inverse", 'escape' => false, 'title' => 'Récupérer une archive contenant les ordres du jour', 'style' => 'margin-left:15px;'));

    echo $this->Html->tag('/div');

    ?>
    <div class="spacer"></div>
    <table style='width:100%'>
        <caption>Liste des acteurs</caption>
        <tr>
            <th class="colonne_checkbox"><input type="checkbox" id="masterCheckbox"/></th>
            <th>Élus</th>
            <th>Document</th>
            <th>Date d'envoi</th>
            <th>statut</th>
        </tr>
        <?php
        $numLigne = 1;
        foreach ($acteurs as $acteur) {
            $rowClass = ($numLigne & 1) ? array('height' => '36px') : array('height' => '36px', 'class' => 'altrow');
            echo $this->Html->tag('tr', null, $rowClass);
            $numLigne++;

            if (file_exists(WEBROOT_PATH . '/files/seances/' . $seance_id . "/$model_id/" . $acteur['Acteur']['id'] . '.pdf')) {
                $filepath = '/files/seances/' . $seance_id . "/$model_id/" . $acteur['Acteur']['id'] . '.pdf';
                $ext = '.pdf';
            } else if (file_exists(WEBROOT_PATH . '/files/seances/' . $seance_id . "/$model_id/" . $acteur['Acteur']['id'] . '.odt')) {
                $filepath = '/files/seances/' . $seance_id . "/$model_id/" . $acteur['Acteur']['id'] . '.odt';
                $ext = '.odt';
            } else {
                $filepath = '';
            }

            echo '<td style="text-align: center; vertical-align: middle;">';
            if (empty($acteur['Acteur']['email']))
                echo $this->Form->checkbox('Acteur.id_' . $acteur['Acteur']['id'], array(
                    'disabled' => true,
                    'title' => 'Envoi impossible, l\'adresse mail de l\'acteur n\'est pas renseigné'));
            elseif (empty($filepath))
                echo $this->Form->checkbox('Acteur.id_' . $acteur['Acteur']['id'], array(
                    'disabled' => true,
                    'title' => "Impossible d'envoyer à cet acteur, l'ordre du jour n'a pas encore été généré."));
            elseif ($acteur['Acteur']['date_envoi'] == null)
                echo $this->Form->checkbox('Acteur.id_' . $acteur['Acteur']['id'], array('class' => 'checkbox_acteur_odj'));
            else
                echo '<i class="fa fa-check" title="ODJ déjà envoyé"></i>';
            echo '</td>';

            echo '<td>' . $this->Html->link($acteur['Acteur']['prenom'] . ' ' . $acteur['Acteur']['nom'], array('controller'=>'acteurs', 'action'=>'view', $acteur['Acteur']['id'])) . '</td>';

            if (!empty($filepath))
                echo('<td>' . $this->Html->link($model['Modeltemplate']['name'] . $ext, $filepath) . ' : [' . $date_convocation . ']</td>');
            else
                echo('<td></td>');
            if ($acteur['Acteur']['date_envoi'] == null)
                echo('<td>Non envoyé</td>');
            else
                echo('<td>' . 'Envoyé le : ' . $this->Form2->ukToFrenchDateWithHour($acteur['Acteur']['date_envoi']) . '</td>');

            if ($acteur['Acteur']['date_reception'] == null) {
                if ($use_mail_securise)
                    echo('<td>Non reçu</td>');
                else
                    echo('<td>Pas d\'accusé de réception</td>');
            } else
                echo('<td>' . 'Reçu le : ' . $this->Form2->ukToFrenchDateWithHour($acteur['Acteur']['date_reception']) . '</td>');
        }
        ?>
        </tr>
    </table>

    <div class="spacer"></div>

    <div class="submit btn-group">
        <?php echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', $previous, array('escape' => false, 'class' => 'btn')); ?>
        <?php echo $this->Form->button("<i class='fa fa-envelope'></i> Envoyer l'ordre du jour <span id='nbActeursChecked'></span>", array('id' => 'envoyer_odj', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Envoyer les ordres du jour par email aux acteurs sélectionnés')); ?>
    </div>


    <?php echo $this->Form->end(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        //Lors d'action sur une checkbox :
        $('input[type=checkbox]').change(selectionChange);
        selectionChange();
    });
    function selectionChange() {
        var nbChecked = $('input[type=checkbox].checkbox_acteur_odj:checked').length;
        //Apposer ou non la class disabled au bouton selon si des checkbox sont cochées (style)
        if (nbChecked > 0) {
            $('#envoyer_odj').removeClass('disabled');
            $("#envoyer_odj").prop("disabled", false);
        } else {
            $('#envoyer_odj').addClass('disabled');
            $("#envoyer_odj").prop("disabled", true);
        }
        $('#nbActeursChecked').text('(' + nbChecked + ')');
    }
</script>