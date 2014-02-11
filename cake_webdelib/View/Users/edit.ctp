<?php
echo $this->Html->script('jstree.min');
echo $this->Html->css('jstree/style.min');
echo $this->element('onglets', array('listeOnglets' => array(
    'Informations principales',
    'Droits',
    'Types d\'acte')));

if ($this->Html->value('User.id')) {
    echo "<h2>Modification d'un utilisateur</h2>";
    echo $this->Form->create('User', array('url' => '/users/edit/' . $this->Html->value('User.id'), 'type' => 'post', 'name' => 'userEdit', 'id' => 'userForm'));
} else {
    echo "<h2>Ajout d'un utilisateur</h2>";
    echo $this->Form->create('User', array('url' => '/users/add', 'type' => 'post', 'id' => 'userForm'));
}
?>
<div id='tab1'>
    <fieldset>
        <legend>Identification de connexion</legend>
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
            <legend>Autres informations</legend>
            <?php echo $this->Form->input('User.profil_id', array('label' => 'Profil utilisateur <abbr title="obligatoire">*</abbr>', 'options' => $profils, 'empty' => false)); ?>
            <br/>
            <label>Notification email</label>
            <?php
            echo $this->Form->input('User.accept_notif', array(
                'type' => 'radio',
                'options' => $notif,
                'legend' => false,
                'onClick' => "if(this.value==1) $('#mails').show(); else $('#mails').hide();"));
            ?>
        </fieldset>
        <br/>

        <?php
        if ($this->data['User']['accept_notif'] == 0)
            echo("<fieldset id='mails' style='display:none;'>");
        else
            echo("<fieldset id='mails'>");
        ?>
        <legend>Notifications</legend>
        <?php echo $this->Form->input('User.mail_insertion',
            array('type' => 'radio', 'legend' => false, 'options' => $notif, 'before' => '<strong>Insertion</strong>')); ?>
        <?php echo $this->Form->input('User.mail_traitement',
            array('type' => 'radio', 'legend' => false, 'options' => $notif, 'before' => '<strong>Traitement</strong>')); ?>
        <?php echo $this->Form->input('User.mail_refus',
            array('type' => 'radio', 'legend' => false, 'options' => $notif, 'before' => '<strong>Refus</strong>')); ?>
        <?php echo $this->Form->input('User.mail_modif_projet_cree',
            array('type' => 'radio', 'legend' => false, 'options' => $notif, 'before' => '<strong>Mon projet est modifié</strong>')); ?>
        <?php echo $this->Form->input('User.mail_modif_projet_valide',
            array('type' => 'radio', 'legend' => false, 'options' => $notif, 'before' => '<strong>Un projet que j&apos;ai visé est modifié</strong>')); ?>
        <?php echo $this->Form->input('User.mail_retard_validation',
            array('type' => 'radio', 'legend' => false, 'options' => $notif, 'before' => '<strong>Retard sur la validation d\'un projet</strong>')); ?>
        <div class="spacer"></div>
        <hr/>
        <?php
        echo '</fieldset>';
        echo '<div class="spacer"></div>';
        echo $this->Form->input('Circuit.Circuit', array('label' => 'Circuits visibles par l\'utilisateur',
            'options' => $circuits,
            'onchange' => 'onchangeCircuitDefault();',
            'multiple' => true,
            'id' => 'all_circuits',
            'size' => 15,
            'empty' => true));

        echo "<div class='spacer'></div>";

        echo $this->Form->input('User.circuit_defaut_id', array('type' => 'select',
            'id' => 'default_circuit',
            'label' => "Circuit par défaut",
            'options' => array()));
        ?>
    </div>
    <div class="demi">
        <fieldset id="services-fieldset">
            <legend>Services</legend>
            <input id="search_service" placeholder="Filtrer par nom de service"/>
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
        </fieldset>
        <?php
        echo $this->Form->error('User.Service', 'Sélectionnez un ou plusieurs services');
        ?>
    </div>
    <div class="spacer"></div>
</div>

<div id='tab2' style="display: none;">
    <?php
    if ($this->Html->value('User.id'))
        echo $this->element('editDroits');
    else {
        echo $this->Html->para(null, __('Sauvegardez puis &eacute;ditez &agrave; nouveau l\'utilisateur pour modifier ses droits.', true));
        echo $this->Html->para(null, __('Les nouveaux utilisateurs h&eacute;ritent des droits des profils auxquels ils sont rattach&eacute;s.', true));
    }
    ?>
</div>

<div id='tab3' style="display: none;">
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
</div>

<div id='tab4' style="display: none;">
    <?php // echo $this->element('configuration_synthese');  ?>
</div>


<div class="submit">
    <?php if ($this->action == 'edit') echo $this->Form->hidden('User.id'); ?>

    <?php $this->Html2->boutonsSaveCancel('', 'index', ''); ?>

    <?php // echo $this->Form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter')); ?>
    <?php // echo $this->Html->link('Annuler', '/users/index', array('class'=>'link_annuler', 'name'=>'Annuler')) ?>
</div>
<?php echo $this->Form->end(); ?>
<script>
    $(document).ready(function () {
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
                "show_only_matches": true, //Masque les résultats ne correspondant pas
                "close_opened_onclear": false //Ne pas déselectionner les résultats ne correspondant pas
            },
            "types" : {
                "level0" : {
                    "icon" : "fa fa-sitemap"
                },
                "default" : {
                    "icon" : "fa fa-users"
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
        services.forEach(function(entry){
            $('#services').jstree('select_node', 'Service_'+entry);
        });
        $('#services').jstree('set_type', $());

        $('#services').on('changed.jstree',function (e, data) {
            /* Listener onChange qui fait la synchro jsTree/hiddenField */
            var i, j, r = [];
            for (i = 0, j = data.selected.length; i < j; i++) {
                r.push(data.instance.get_node(data.selected[i]).data.id);
            }
            $('#ServiceService').val(r.join(','));
        });

        /* Recherche dans la liste jstree */
        var to = false;
        $('#search_service').keyup(function () {
            if (to) {
                clearTimeout(to);
            }
            to = setTimeout(function () {
                var v = $('#search_service').val();
                console.log(v);
                $('#services').jstree(true).search(v);
            }, 250);
        });

        onchangeCircuitDefault();
    });

    function onchangeCircuitDefault() {
        var selected_default_circuit_id = $('#default_circuit').val();
        if (selected_default_circuit_id == null)
            selected_default_circuit_id = <?php if (is_int($selectedCircuits)) echo $selectedCircuits; else echo 'null'; ?>;
        $('#default_circuit').empty();
        $('#all_circuits').find("option:selected").each(function (index, element) {
            $(element).clone().appendTo('#default_circuit');
        });
        $('#default_circuit').val(selected_default_circuit_id);
    }
</script>

<style>
    .input.radio label, .input.radio input {
        padding-left: 2px;
        padding-right: 5px;
        margin: 0;
        width: auto;
    }
    #services {
        max-width: 100%;
        overflow: auto;
        box-shadow: 0 0 5px #ccc;
        border-radius: 5px;
        padding: 10px;
    }
</style>