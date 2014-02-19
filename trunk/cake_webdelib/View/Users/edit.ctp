<?php
echo $this->Html->script('jstree.min');
echo $this->Html->css('jstree/style.min');
echo $this->Html->css('users');

if ($this->Html->value('User.id')) {
    echo "<h2>Modification de l'utilisateur n&deg;{$this->Html->value('User.id')} : &quot;{$this->Html->value('User.login')}&quot;</h2>";
    echo $this->Form->create('User', array('url' => '/users/edit/' . $this->Html->value('User.id'), 'type' => 'post', 'name' => 'userEdit', 'id' => 'userForm'));
} else {
    echo "<h2>Création d'un utilisateur</h2>";
    echo $this->Form->create('User', array('url' => '/users/add', 'type' => 'post', 'id' => 'userForm'));
}

echo $this->element('onglets', array('listeOnglets' => array(
    'Informations principales',
    'Droits',
    'Types d\'acte')));
?>
<div id='tab1'>
    <fieldset>
        <legend>Identifiant de connexion</legend>
        <?php
        echo $this->Form->input('User.login', array('label' => 'Login <abbr title="obligatoire">*</abbr>'));

        if (!$this->Html->value('User.id')) {
            echo "<div class='tiers'>";
            echo $this->Form->input('User.password', array('type' => 'password', 'label' => 'Password <abbr title="obligatoire">*</abbr>'));
            echo "</div>";
            echo "<div class='tiers'>";
            echo $this->Form->input('User.password2', array('type' => 'password', 'label' => 'Confirmez le password <abbr title="obligatoire">*</abbr>'));
            echo "</div>";
        }
        ?>
    </fieldset>

    <div class="spacer"></div>

    <fieldset>
        <legend>Identité et contacts</legend>
        <div class="demi">
            <?php echo $this->Form->input('User.nom', array('label' => 'Nom <abbr title="obligatoire">*</abbr>', 'size' => '30')); ?>
            <br/>
            <?php echo $this->Form->input('User.prenom', array('label' => 'Prénom <abbr title="obligatoire">*</abbr>', 'size' => '30')); ?>
        </div>
        <div class="demi">
            <?php echo $this->Form->input('User.telfixe', array('label' => 'Tel fixe')); ?>
            <br/>
            <?php echo $this->Form->input('User.telmobile', array('label' => 'Tel mobile')); ?>
            <br/>
            <?php echo $this->Form->input('User.email', array('label' => 'Email', 'size' => '30')); ?>
            <br/>
            <?php echo $this->Form->input('User.note', array('type' => 'textarea', 'cols' => '30', 'rows' => '2')); ?>
        </div>
    </fieldset>


    <div class="spacer"></div>

    <div class="demi">
        <fieldset>
            <legend>Profil</legend>
            <?php echo $this->Form->input('User.profil_id', array(
                'label' => array('text' => 'Profil utilisateur <abbr title="obligatoire">*</abbr>', 'class' => 'label_autocomplete'),
                'options' => $profils,
                'empty' => false,
                'class' => 'autocomplete'
            ));
            ?>
        </fieldset>
        <div class="spacer"></div>
        <fieldset>
            <legend>Notifications</legend>
            <div class="spacer"></div>
            <label id="label_notifications">Recevoir des alertes par email</label>
            <?php
            echo $this->Form->input('User.accept_notif', array(
                'type' => 'radio',
                'options' => $notif,
                'legend' => false,
                'onClick' => "if(this.value==1) $('#mails').show(); else $('#mails').hide();"));
            ?>
            <div id="mails"<?php echo ($this->data['User']['accept_notif'] == 0) ? " style='display:none;'" : ''; ?>>
                <hr/>
                <?php echo $this->Form->input('User.mail_insertion',
                    array('type' => 'radio', 'legend' => false, 'options' => $notif, 'before' => '<strong>Insertion</strong>')); ?>
                <?php echo $this->Form->input('User.mail_traitement',
                    array('type' => 'radio', 'legend' => false, 'options' => $notif, 'before' => '<strong>Traitement en attente</strong>')); ?>
                <?php echo $this->Form->input('User.mail_refus',
                    array('type' => 'radio', 'legend' => false, 'options' => $notif, 'before' => '<strong>Projet refusé</strong>')); ?>
                <?php echo $this->Form->input('User.mail_modif_projet_cree',
                    array('type' => 'radio', 'legend' => false, 'options' => $notif, 'before' => '<strong>Un de mes projets est modifié</strong>')); ?>
                <?php echo $this->Form->input('User.mail_modif_projet_valide',
                    array('type' => 'radio', 'legend' => false, 'options' => $notif, 'before' => '<strong>Un projet que j&apos;ai visé est modifié</strong>')); ?>
                <?php echo $this->Form->input('User.mail_retard_validation',
                    array('type' => 'radio', 'legend' => false, 'options' => $notif, 'before' => '<strong>Retard de validation</strong>')); ?>
            </div>
            <div class="spacer"></div>
        </fieldset>
        <?php
        echo $this->Html->tag('fieldset', null);
        echo $this->Html->tag('legend', 'Circuits de validation');
        echo $this->Form->input('Circuit.Circuit', array(
            'label' => array('text' => 'Circuits visibles par l\'utilisateur', 'id' => 'label_circuits'),
            'options' => $circuits,
            'onchange' => 'onchangeCircuitDefault();',
            'multiple' => true,
            'id' => 'all_circuits',
            'class' => 'autocomplete',
        ));

        echo "<div class='spacer'></div>";

        echo $this->Form->input('User.circuit_defaut_id', array(
            'type' => 'select',
            'class' => 'autocomplete',
            'id' => 'default_circuit',
            'label' => array('text' => 'Circuit par défaut', 'class' => 'label_autocomplete'),
            'empty' => '',
            'options' => array()
        ));
        echo $this->Html->tag('/fieldset');
        ?>
    </div>
    <div class="demi">
        <fieldset id="services-fieldset">
            <legend style="margin-bottom: 0;">Services</legend>
            <div id="services_bloc">
                <div class="input-append">
                    <input type="text" id="search_service" placeholder="Filtrer par nom de service"
                           style="float: left; z-index: 2"/>

                    <div class="btn-group">
                        <a class="btn" id="search_service_button" title="Lancer la recherche"><i
                                class="fa fa-search"></i></a>

                        <a class="btn dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a id="search_service_erase_button" title="Remettre à zéro la recherche">Effacer la recherche</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a id="search_service_ascenceur_button" title="Désactiver l'ascenceur vertical">Désactiver défilement</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a id="search_service_plier_button" title="Replier tous les services">Tout replier</i></a>
                            </li>
                            <li>
                                <a id="search_service_deplier_button" title="Déplier tous les services">Tout déplier</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a id="search_service_cocher_button" title="Cocher tous les services">Tout cocher</i></a>
                            </li>
                            <li>
                                <a id="search_service_decocher_button" title="Déplier tous les services">Tout décocher</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="spacer"></div>
                <div id="services">
                    <?php
                    $selectedServices = !empty($selectedServices) ? $selectedServices : array();
                    echo $this->Tree->generateList($services, 'Service', $selectedServices);
                    ?>
                </div>
                <?php
                $options = array();
                if (!empty($selectedServices))
                    $options['value'] = implode(',', $selectedServices);
                elseif ($this->Html->value('Service.Service'))
                    $options['value'] = implode(',', $this->Html->value('Service.Service'));
                echo $this->Form->hidden('Service.Service', $options);
                ?>
            </div>
        </fieldset>
        <?php
        echo $this->Form->error('User.Service', 'Sélectionnez un ou plusieurs services');
        ?>
    </div>
    <div class="spacer"></div>
</div>

<div id='tab2' style="display: none;">
    <fieldset>
        <legend>Table des droits</legend>
        <?php
        if ($this->Html->value('User.id'))
            echo $this->element('editDroits');
        else {
            echo $this->Html->para(null, __('Sauvegardez puis &eacute;ditez &agrave; nouveau l\'utilisateur pour modifier ses droits.', true));
            echo $this->Html->para(null, __('Les nouveaux utilisateurs h&eacute;ritent des droits des profils auxquels ils sont rattach&eacute;s.', true));
        }
        ?>
    </fieldset>
</div>

<div id='tab3' style="display: none;">
    <fieldset>
        <legend>Types d'acte autorisés</legend>
        <?php
        foreach ($natures as $nature)
            echo $this->Form->input('Nature.id_' . $nature['Typeacte']['id'], array(
                'type' => 'checkbox',
                'checked' => $nature['Nature']['check'],
                'label' => array(
                    'text' => $nature['Typeacte']['libelle'],
                    'class' => 'no-width-limit'
                )));
        ?>
    </fieldset>
</div>

<div id='tab4' style="display: none;">
    <?php // echo $this->element('configuration_synthese');  ?>
</div>


<div class="submit">
    <?php if ($this->action == 'edit') echo $this->Form->hidden('User.id');
    echo $this->Html->tag('div', null, array('class' => 'btn-group', 'style' => 'text-align:center; margin-top:10px;'));
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Annuler', array('action'=>'index'), array('class' => 'btn', 'escape' => false, 'title' => 'Annuler', 'style' => 'float:none;'));
    echo $this->Form->button("<i class='fa fa-save'></i> Sauvegarder", array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Enregistrer les modifications', 'style' => 'float:none;'));
    echo $this->Html->tag('/div', null);
    ?>

</div>
<?php echo $this->Form->end(); ?>
<script>
    $(document).ready(function () {
        $('.autocomplete').select2({
            width: 'resolve',
            placeholder: 'Aucune séléction',
            allowClear: true
        });

        $('#services').jstree({
            /* Initialisation de jstree sur la liste des services */
            "core": { //Paramétrage du coeur du plugin
                "animation": 0, //Pas d'animation (déplier)
                "themes": { "stripes": true } //Une ligne sur deux est grise (meilleure lisibilité)
            },
            "checkbox": { //Paramétrage du plugin checkbox
                "three_state": false //Ne pas propager la séléction parent/enfants
            },
            "search": { //Paramétrage du plugin de recherche
                "fuzzy": false, //Indicates if the search should be fuzzy or not (should chnd3 match child node 3).
                "show_only_matches": true, //Masque les résultats ne correspondant pas
                "case_sensitive": false, //Sensibilité à la casse
                "close_opened_onclear": false //Ne pas déselectionner les résultats ne correspondant pas
            },
            "types": {
                "level0": {
                    "icon": "fa fa-sitemap"
                },
                "default": {
                    "icon": "fa fa-users"
                }
            },
            "plugins": [
                "checkbox", //Affiche les checkboxes
                "wholerow", //Toute la ligne est surlignée
                "search", //Champs de recherche d'élément de la liste (filtre)
                "types"
            ]
        });

        var services = $('#ServiceService').val().split(',');
        services.forEach(function (entry) {
            $('#services').jstree('select_node', 'Service_' + entry);
        });

        $('#services').on('changed.jstree', function (e, data) {
            /* Listener onChange qui fait la synchro jsTree/hiddenField */
            var i, j, r = [];
            for (i = 0, j = data.selected.length; i < j; i++)
                r.push(data.instance.get_node(data.selected[i]).data.id);
            $('#ServiceService').val(r.join(','));
        });

        /* Recherche dans la liste jstree */
        $('#search_service_button').click(function () {
            $('#services').jstree(true).search($('#search_service').val());
        });
        /* Recherche dans la liste jstree */
        $('#search_service_erase_button').click(function () {
            $('#search_service').val('');
            $('#search_service_button').click();
        });
        $('#search_service').keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                $('#search_service_button').click();
                return false;
            }
        });
        $("#search_service_plier_button").click(function () {
            $('#services').jstree('close_all');
        });
        $("#search_service_deplier_button").click(function () {
            $('#services').jstree('open_all');
        });
        $("#search_service_cocher_button").click(function () {
            $('#services').jstree('select_all');
        });
        $("#search_service_decocher_button").click(function () {
            $('#services').jstree('deselect_all');
        });
        $("#search_service_ascenceur_button").click(function () {
            var overflow = $('#services').css('overflow');
            console.log(overflow);
            if (overflow == 'auto'){
                $('#services').css('overflow', 'hidden');
                $('#services').css('max-height', 'none');
                $("#search_service_ascenceur_button").text('Activer défilement');
            }else{
                $('#services').css('overflow', 'auto');
                $('#services').css('max-height', '350px');
                $("#search_service_ascenceur_button").text('Désactiver défilement');
            }
            console.log($('#services').css('overflow'));
        });

        onchangeCircuitDefault();
    });

    function onchangeCircuitDefault() {
        $("#default_circuit").select2("destroy");
        var selected_default_circuit_id = $('#default_circuit').val();

        if (selected_default_circuit_id == null)
            selected_default_circuit_id = <?php if (is_int($selectedCircuits)) echo $selectedCircuits; else echo 'null'; ?>;

        $('#default_circuit').empty();
        $('<option value=""></option>').prependTo('#default_circuit');
        $('#all_circuits').find("option:selected").each(function (index, element) {
            $(element).clone().appendTo('#default_circuit');
        });
        $('#default_circuit').val(selected_default_circuit_id);
        $("#default_circuit").select2({
            width: 'resolve',
            allowClear: true,
            placeholder: 'Aucun'
        });
    }
</script>
