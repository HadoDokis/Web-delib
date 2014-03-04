<div class="deliberations">
    <?php echo $this->Html->script('utils.js'); ?>
    <h2>Envoi des convocations</h2>
    <?php echo $this->Form->create('Seance',
        array(
            'type' => 'file',
            'url' => array('controller' => 'seances', 'action' => 'sendConvocations', $seance_id, $model_id)
        ));
    echo $this->Html->tag('div', null, array('style' => 'padding-right:1em;float:left;'));
    echo $this->Html->link("<i class='fa fa-cogs'></i> Générer les convocations",
        array('controller' => 'seances', 'action' => 'genereFusionToFiles', $seance_id, $model_id, 'convocation'),
        array('class' => "btn btn-primary waiter", 'escape' => false, 'title' => 'Générer le document des convocations', 'data-modal' => 'Génération des convocations en cours'));
    echo $this->Html->tag('/div', null);
    echo $this->Html->link("<i class='fa fa-download'></i> Télécharger le zip des convocations",
        array('controller' => 'seances', 'action' => 'recuperer_zip', $seance_id, $model_id),
        array('class' => "btn btn-inverse", 'escape' => false, 'title' => 'Récupérer une archive contenant les convocations'));
    ?>
    <br>
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
                    'title' => "Impossible d'envoyer à cet acteur, la convocation n'a pas encore été générée."));
            elseif ($acteur['Acteur']['date_envoi'] == null)
                echo $this->Form->checkbox('Acteur.id_' . $acteur['Acteur']['id'], array('class' => 'checkbox_acteur_convoc'));
            else
                echo '<i class="fa fa-check" title="Convocation déjà envoyée"></i>';

            echo '</td>';

            echo('<td>' . $acteur['Acteur']['prenom'] . ' ' . $acteur['Acteur']['nom'] . '</td>');

            if ($filepath != '')
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
    <div class="submit">
        <?php echo $this->Form->button("<i class='fa fa-envelope'></i> Envoyer les convocations <span id='nbActeursChecked'></span>", array('id' => 'envoyer_convocs', 'class' => 'btn btn-info', 'escape' => false, 'title' => 'Envoyer les convocations par email aux acteurs sélectionnés')); ?>
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
        var nbChecked = $('input[type=checkbox].checkbox_acteur_convoc:checked').length;
        //Apposer ou non la class disabled au bouton selon si des checkbox sont cochées (style)
        if (nbChecked > 0) {
            $('#envoyer_convocs').removeClass('disabled');
            $("#envoyer_convocs").prop("disabled", false);
        } else {
            $('#envoyer_convocs').addClass('disabled');
            $("#envoyer_convocs").prop("disabled", true);
        }
        $('#nbActeursChecked').text('(' + nbChecked + ')');
    }
</script>

<style>
    table tr td, table tr th {
        vertical-align: middle;
    }
</style>