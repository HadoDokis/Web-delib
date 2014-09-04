<?php
//echo $this->Html->css('users');
if ($this->Html->value('User.id')) {
    echo $this->Bs->tag('h3', 'Modification de l\'utilisateur n&deg;' . $this->Html->value('User.id') . ' : ' . $this->Html->value('User.login'));
    echo $this->BsForm->create('User', array('url' => array('controller' => 'users', 'action' => 'edit', $this->Html->value('User.id')), 'type' => 'post', 'name' => 'userEdit', 'id' => 'userForm'));
} else {
    echo $this->Bs->tag('h3', 'Création d\'un utilisateur');
    echo $this->BsForm->create('User', array('url' => array('controller' => 'users', 'action' => 'add'), 'type' => 'post', 'id' => 'userForm'));
}

echo $this->Bs->tab(array(
    'infos' => 'Informations principales',
    'droits' => 'Droits',
    'notifications' => 'Notifications',
    'circuit_services' => 'Circuit & Services',
    'types' => 'Types d\'acte'), array('active' => isset($nOngletCourant) ? $nOngletCourant : 'infos', 'class' => '-justified')) .
 $this->Bs->tabContent();

echo $this->Bs->tabPane('infos', array('class' => isset($nOngletCourant) ? $nOngletCourant : 'active')) .
 $this->Html->tag(null, '<br />') .
$this->Html->tag('div', null, array('class' => 'panel panel-default')) .
        $this->Html->tag('div', 'Identifiant de connexion', array('class' => 'panel-heading')) .
        $this->Html->tag('div', null, array('class' => 'panel-body')) .
        $this->BsForm->input('User.login', array('label' => 'Login <abbr title="obligatoire">*</abbr>', 'required'));
if (!$this->Html->value('User.id')) {
    echo "<div class='tiers'>";
    echo $this->BsForm->input('User.password', array('type' => 'password', 'label' => 'Password <abbr title="obligatoire">*</abbr>'));
    echo "</div>";
    echo "<div class='tiers'>";
    echo $this->BsForm->input('User.password2', array('type' => 'password', 'label' => 'Confirmez le password <abbr title="obligatoire">*</abbr>'));
    echo "</div>";
}
echo $this->Bs->close(2) . $this->Html->tag(null, '<br />');

echo $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
 $this->Html->tag('div', 'Identité et contacts', array('class' => 'panel-heading')) .
 $this->Html->tag('div', null, array('class' => 'panel-body')) .
 $this->Bs->row() .
 $this->Bs->col('lg6') .
 $this->BsForm->input('User.nom', array('label' => 'Nom <abbr title="obligatoire">*</abbr>', 'required')) .
 $this->BsForm->input('User.prenom', array('label' => 'Prénom <abbr title="obligatoire">*</abbr>', 'required')) .
 $this->BsForm->input('User.telfixe', array('label' => 'Tel fixe')) .
 $this->BsForm->input('User.telmobile', array('label' => 'Tel mobile')) .
 $this->Bs->close() .
 $this->Bs->col('lg6') .
 $this->BsForm->input('User.email', array('label' => 'Email', 'type' => 'email')) .
 $this->BsForm->input('User.note', array('type' => 'textarea', 'cols' => '30', 'rows' => '5')) .
 $this->Bs->close(4) . $this->Html->tag(null, '<br />');


echo $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
 $this->Html->tag('div', 'Profil', array('class' => 'panel-heading')) .
 $this->Html->tag('div', null, array('class' => 'panel-body')) .
 $this->BsForm->input('User.profil_id', array(
    'label' => array('text' => 'Profil utilisateur <abbr title="obligatoire">*</abbr>', 'class' => 'label_autocomplete'),
    'options' => $profils,
    'empty' => false,
    'class' => 'autocomplete'
)) .
 $this->Bs->close(2) .
 $this->Bs->tabClose();

echo $this->Bs->tabPane('notifications').
$this->Html->tag('h4', 'Paramètres de Notification') .
$this->Html->tag('p',     
        'Nous vous notifierons par email à chaque fois qu\'il arrive quelque chose vous concernant.'.
        ' Les paramètres ci-dessous vous permettront de contrôler la façon dont vous souhaitez être averti.');
$this->BsForm->setLeft(0);
echo $this->BsForm->checkbox('User.accept_notif', array(
        'label' => 'Ne jamais notifier',
        'autocomplete'=>'off',
        'checked'=> empty($this->data['User']['accept_notif'])?true:false));
$this->BsForm->setLeft(3);
echo $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
$this->Html->tag('div', 'Me notifier lorsque:', array('class' => 'panel-heading')) .
$this->Html->tag('div', null, array('class' => 'panel-body')).
        $this->BsForm->radio('User.mail_insertion',$notif, array( 'label' => 'Insertion')).
        $this->BsForm->radio('User.mail_traitement',$notif, array( 'label' => 'Traitement en attente')).
        $this->BsForm->radio('User.mail_refus',$notif, array( 'label' => 'Projet refusé')).
        $this->BsForm->radio('User.mail_modif_projet_cree',$notif, array( 'label' => 'Un de mes projets est modifié')).
        $this->BsForm->radio('User.mail_modif_projet_valide',$notif, array( 'label' => 'Un projet que j&apos;ai visé est modifié')).
        $this->BsForm->radio('User.mail_retard_validation',$notif, array( 'label' => 'Retard de validation')).
 $this->Bs->close(2) .
 $this->Bs->tabClose();
 

echo $this->Bs->tabPane('circuit_services') .
$this->Html->tag(null, '<br />') .
 $this->Bs->row() .
 $this->Bs->col('lg6') .
 $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
 $this->Html->tag('div', 'Circuits de validation', array('class' => 'panel-heading')) .
    $this->Html->tag('div', null, array('class' => 'panel-body')) .
 $this->BsForm->input('Circuit.Circuit', array(
    'label' => array('text' => 'Circuits visibles par l\'utilisateur', 'id' => 'label_circuits'),
    'options' => $circuits,
    'onchange' => 'onchangeCircuitDefault();',
    'multiple' => true,
    'id' => 'all_circuits',
    'class' => 'autocomplete',
)) .
 $this->BsForm->input('User.circuit_defaut_id', array(
    'type' => 'select',
    'class' => 'autocomplete',
    'id' => 'default_circuit',
    'label' => array('text' => 'Circuit par défaut', 'class' => 'label_autocomplete'),
    'empty' => '',
    'options' => array()
)).
$this->Bs->close(3);

echo $this->Bs->col('lg6') .
 $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
 $this->Html->tag('div', 'Services', array('class' => 'panel-heading')).
    $this->Html->tag('div', null, array('class' => 'panel-body')) ;
?>
<div class="input-append">
    <input type="text" id="search_service" placeholder="Filtrer par nom de service"
           style="float: left; z-index: 2"/>

    <div class="btn-group">
        <a class="btn" id="search_tree_button" title="Lancer la recherche"><i
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
<?php
$selectedServices = !empty($selectedServices) ? $selectedServices : array();
echo $this->Tree->generateList($services, 'Service', $selectedServices);
$options = array();
if (!empty($selectedServices))
    $options['value'] = implode(',', $selectedServices);
elseif ($this->Html->value('Service.Service'))
    $options['value'] = implode(',', $this->Html->value('Service.Service'));
echo $this->BsForm->hidden('Service.Service', $options)
        .$this->BsForm->error('User.Service', 'Sélectionnez un ou plusieurs services') .
$this->Bs->close(4).
 $this->Bs->tabClose();

echo $this->Bs->tabPane('droits') .
        $this->Html->tag(null, '<br />') .
 $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
 $this->Html->tag('div', 'Table des droits', array('class' => 'panel-heading')).
 $this->Html->tag('div', null, array('class' => 'panel-body')) ;
if ($this->Html->value('User.id'))
    echo $this->element('editDroits');
else {
    echo $this->Html->para(null, __('Sauvegardez puis &eacute;ditez &agrave; nouveau l\'utilisateur pour modifier ses droits.', true));
    echo $this->Html->para(null, __('Les nouveaux utilisateurs h&eacute;ritent des droits des profils auxquels ils sont rattach&eacute;s.', true));
}
echo $this->Bs->close(2);
echo $this->Bs->tabClose();

echo $this->Bs->tabPane('types') .
        $this->Html->tag(null, '<br />') .
 $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
 $this->Html->tag('div', 'Types d\'acte autorisés', array('class' => 'panel-heading')).
 $this->Html->tag('div', null, array('class' => 'panel-body'));
$this->BsForm->setFormType('vertical');
foreach ($natures as $nature)
    echo $this->BsForm->checkbox('Nature.id_' . $nature['Typeacte']['id'], 
            array('checked' => $nature['Nature']['check'],
            'label' => $nature['Typeacte']['libelle'])
    );
echo $this->Bs->close(2).
$this->Bs->tabClose().
$this->Bs->tabPaneClose();
//echo $this->Bs->tabPane('configuration_synthese');.
//echo $this->Bs->tabClose();
?>
    <?php
    if ($this->action == 'edit')
        echo $this->Form->hidden('User.id');
    $this->BsForm->setLeft(0);
    echo $this->Html2->btnSaveCancel( null, array('action' => 'index'));
    
   /* echo $this->Html->tag('div', null, array('class' => 'btn-group', 'style' => 'text-align:center; margin-top:10px;'));
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Annuler', array('action' => 'index'), array('class' => 'btn', 'escape' => false, 'title' => 'Annuler', 'style' => 'float:none;'));
    echo $this->Form->button("<i class='fa fa-save'></i> Sauvegarder", array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Enregistrer les modifications', 'style' => 'float:none;'));
    echo $this->Html->tag('/div', null);*/
    ?>
<?php echo $this->BsForm->end(); ?>
<script>
    $(document).ready(function() {
        $('.autocomplete').select2({
            width: 'resolve',
            placeholder: 'Aucune sélection',
            allowClear: true
        });

        $('#services').jstree({
            /* Initialisation de jstree sur la liste des services */
            "core": {//Paramétrage du coeur du plugin
                "animation": 0, //Pas d'animation (déplier)
                "themes": {"stripes": true} //Une ligne sur deux est grise (meilleure lisibilité)
            },
            "checkbox": {//Paramétrage du plugin checkbox
                "three_state": false //Ne pas propager la séléction parent/enfants
            },
            "search": {//Paramétrage du plugin de recherche
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
            "contextmenu": {
                "items": contextMenu
            },
            "plugins": [
                "checkbox", //Affiche les checkboxes
                "wholerow", //Toute la ligne est surlignée
                "search", //Champs de recherche d'élément de la liste (filtre)
                "types", //Pour les icônes
                "contextmenu" //Menu clic droit
            ]
        });

        var services = $('#ServiceService').val().split(',');
        services.forEach(function(entry) {
            $('#services').jstree('select_node', 'Service_' + entry);
        });

        $('#services').on('changed.jstree', function(e, data) {
            /* Listener onChange qui fait la synchro jsTree/hiddenField */
            var i, j, r = [];
            for (i = 0, j = data.selected.length; i < j; i++)
                r.push(data.instance.get_node(data.selected[i]).data.id);
            $('#ServiceService').val(r.join(','));
        });

        /* Recherche dans la liste jstree */
        $('#search_tree_button').click(function() {
            $('#services').jstree(true).search($('#search_service').val());
        });
        /* Recherche dans la liste jstree */
        $('#search_service_erase_button').click(function() {
            $('#search_service').val('');
            $('#services').jstree(true).clear_search();
        });
        $('#search_service').keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                $('#search_tree_button').click();
                return false;
            }
        });
        $("#search_service_plier_button").click(function() {
            $('#services').jstree('close_all');
        });
        $("#search_service_deplier_button").click(function() {
            $('#services').jstree('open_all');
        });
        $("#search_service_cocher_button").click(function() {
            $('#services').jstree('select_all');
        });
        $("#search_service_decocher_button").click(function() {
            $('#services').jstree('deselect_all');
        });
        $("#search_service_ascenceur_button").click(function() {
            var overflow = $('#services').css('overflow');
            if (overflow == 'auto') {
                $('#services')
                        .css('overflow', 'hidden')
                        .css('max-height', 'none');
                $("#search_service_ascenceur_button").text('Activer défilement');
            } else {
                $('#services')
                        .css('overflow', 'auto')
                        .css('max-height', '350px');
                $("#search_service_ascenceur_button").text('Désactiver défilement');
            }
        });

        onchangeCircuitDefault();
    });

    function contextMenu(node) {
        // The default set of all items
        var items = {
            "closeSearch": {
                "label": "Effacer la recherche",
                "action": function() {
                    $('#search_service').val('');
                    $('#services').jstree(true).clear_search();
                },
                "icon": "fa fa-eraser",
                "separator_after": true
            },
            "selectAll": {
                "label": "Tout cocher",
                "action": function() {
                    $('#services').jstree('select_all');
                },
                "icon": "fa fa-check-square-o"
            },
            "deselectAll": {
                "label": "Tout décocher",
                "action": function() {
                    $('#services').jstree('deselect_all');
                },
                "icon": "fa fa-square-o",
                "separator_after": true
            },
            "openAll": {
                "label": "Tout déplier",
                "action": function() {
                    $('#services').jstree('open_all');
                },
                "icon": "fa fa-plus-square-o"
            },
            "closeAll": {
                "label": "Tout replier",
                "action": function() {
                    $('#services').jstree('close_all');
                },
                "icon": "fa fa-minus-square-o"
            }
        };
        if ($('#search_service').val().length == 0) {
            delete items.closeSearch;
        }
        return items;
    }

    function onchangeCircuitDefault() {
        $("#default_circuit").select2("destroy");
        var selected_default_circuit_id = $('#default_circuit').val();

        if (selected_default_circuit_id == '' || selected_default_circuit_id == null)
            selected_default_circuit_id = <?php if (is_int($this->Html->value('User.circuit_defaut_id'))) echo $this->Html->value('User.circuit_defaut_id');
else echo 'null'; ?>;

        $('#default_circuit').empty();
        $('<option value=""></option>').prependTo('#default_circuit');
        $('#all_circuits').find("option:selected").each(function(index, element) {
            $(element).clone().removeAttr('selected').appendTo('#default_circuit');
        });
        $("#default_circuit")
                .val(selected_default_circuit_id)
                .select2({
                    width: 'resolve',
                    allowClear: true,
                    placeholder: 'Aucun'
                });
    }
</script>
