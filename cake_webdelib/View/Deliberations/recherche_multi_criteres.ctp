<h2><?php echo $titreVue; ?></h2>
<?php echo $this->Form->create('Deliberation', array('type' => 'file', 'url' => $action, 'name' => 'Deliberation', 'class' => 'waiter', 'data-modal' => 'Recherche en cours')); ?>

<div id="add_form" class="recherchediv">
    <table class="sample">
        <tr>
            <td style="max-width: 20%"></td>
            <td style="width: 80%"></td>
        </tr>
        <tr>
            <td>
                <?php echo $this->Form->input('Deliberation.id', array(
                    'type' => 'number',
                    'between' => '</td><td>',
                    'min' => 1,
                    'label' => 'Identifiant du projet',
                    'style' => 'width: 100px'
                ));
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $this->Form->input('Deliberation.texte', array(
                    'between' => '</td><td>',
                    'label' => 'Libellé *',
                    'style' => 'width: 98%'
                ));
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $this->Form->input('Deliberation.typeacte_id', array(
                    'label' => 'Nature',
                    'options' => $this->Session->read('user.Nature'),
                    'empty' => true,
                    'between' => '</td><td>',
                    'escape' => false));
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $this->Form->input('Deliberation.rapporteur_id', array(
                    'between' => '</td><td>',
                    'label' => 'Rapporteur',
                    'options' => $rapporteurs,
                    'empty' => true));
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $this->Form->input('Deliberation.seance_id', array(
                    'between' => '</td><td>',
                    'label' => 'Date séance (et) ',
                    'options' => $date_seances,
                    'multiple' => true,
                    'empty' => false,
                    'size' => '10'));
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $this->Form->input('Deliberation.service_id', array(
                    'between' => '</td><td>',
                    'label' => 'Service Emetteur',
                    'options' => $services,
                    'empty' => true,
                    'escape' => false));
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $this->Form->input('Deliberation.theme_id', array(
                    'between' => '</td><td>',
                    'label' => 'Thème',
                    'escape' => false,
                    'options' => $themes,
                    'default' => $this->Html->value('Deliberation.theme_id'),
                    'empty' => true));
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $this->Form->input('Deliberation.circuit_id', array(
                    'between' => '</td><td>',
                    'label' => 'Circuit ',
                    'options' => $circuits,
                    'default' => $this->Html->value('Deliberation.circuit_id'),
                    'empty' => true));
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $this->Form->input('Deliberation.etat', array(
                    'between' => '</td><td>',
                    'label' => 'Etat',
                    'options' => $etats,
                    'default' => $this->Html->value('Deliberation.etat'),
                    'empty' => true));
                ?>
            </td>
        </tr>
        <?php foreach ($infosupdefs as $infosupdef) {
            $fieldName = 'Infosup.' . $infosupdef['Infosupdef']['id'];
            echo '<tr>';
            echo '<td>' . $this->Form->label($fieldName, $infosupdef['Infosupdef']['nom']) . '</td>';
            echo '<td>';
            if ($infosupdef['Infosupdef']['type'] == 'text' || $infosupdef['Infosupdef']['type'] == 'richText') {
                echo $this->Form->input($fieldName, array('label' => false, 'title' => $infosupdef['Infosupdef']['commentaire']));
            } elseif ($infosupdef['Infosupdef']['type'] == 'date') {
                echo $this->Form->input($fieldName, array('label' => false, 'size' => '9', 'div' => false, 'title' => $infosupdef['Infosupdef']['commentaire']));
                echo '&nbsp;';
                $fieldId = "'Deliberation.Infosup" . Inflector::camelize($infosupdef['Infosupdef']['id']) . "'";
                echo $this->Html->link($this->Html->image("calendar.png", array('border' => '0')), "javascript:show_calendar($fieldId, 'f');", array('escape' => false), false, false);
            } elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
                echo $this->Form->input($fieldName, array('label' => false, 'options' => $listeBoolean, 'empty' => true));
            } elseif ($infosupdef['Infosupdef']['type'] == 'list') {
                echo $this->Form->input($fieldName, array('label' => false, 'options' => $infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty' => true));
            } elseif ($infosupdef['Infosupdef']['type'] == 'listmulti') {
                echo $this->Form->input($fieldName, array('label' => false, 'options' => $infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty' => true, 'multiple' => true, 'class' => 'select2-infosup'));
            }
            echo '</td>';
            echo '</tr>';
        } ?>
        <tr>
            <td><?php echo $this->Form->label('Deliberation.generer', 'Générer le document'); ?> </td>
            <td>
                <?php echo $this->Form->input('Deliberation.generer', array('type' => 'checkbox', 'label' => false, 'div' => false, 'style' => 'float:left; margin-right:15px;')); ?>
                <?php echo $this->Form->input('Deliberation.model', array('label' => false, 'options' => $models, 'div' => array('style' => 'display:none; float:left; margin-top:-3px; min-width:220px;', 'id' => 'DeliberationModeltemplate'))); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <?php
                echo $this->Form->button('<i class="fa fa-search"></i> Rechercher', array('type' => 'submit', 'div' => false, 'class' => 'btn btn-primary', 'name' => 'Rechercher', 'style' => 'margin-bottom:10px;', 'id' => 'submitSearchForm'));
                ?>
            </td>
        </tr>
    </table>
</div>
<br/>
<p>* : le caractère % permet d'affiner les recherches comme indiqué ci-dessous :
<ul>
    <li>Commence par : texte% (si on recherche une information qui commence par 'Département' on écrit comme critère
        de recherche : Département%)
    </li>
    <li>Comprend : %texte% (si on recherche une information qui comprend 'avril' on écrit comme critère de recherche
        : %avril%)
    </li>
    <li>Finit par : %texte (si on recherche une information qui finit par 'clos.' on écrit comme critère de
        recherche : %clos.)
    </li>
</ul>
</p>

<script type="application/javascript">
    $(document).ready(function () {
        $("#DeliberationGenerer").change(function(){
            if($(this).prop('checked')){
                $('#DeliberationModeltemplate').show();
                $('#submitSearchForm').html("<i class='fa fa-file-text'></i> Générer le document");
            }
            else {
                $('#DeliberationModeltemplate').hide();
                $('#submitSearchForm').html("<i class='fa fa-search'></i> Rechercher");
            }
        });
        $("#DeliberationGenerer").prop('checked', false);
        $('select').select2({
            width: "100%",
            allowClear: true,
            placeholder: 'Aucune sélection',
            formatSelection: function (object, container) {
                // trim sur la sélection (affichage en arbre)
                return $.trim(object.text);
            }
        });
    });
</script>