<?php
$this->Html->addCrumb('Liste des services');

echo $this->Bs->tag('h3', 'Liste des services');

echo $this->Bs->div('btn-toolbar', null, array('role'=>"toolbar"));
echo $this->Bs->div('btn-group', null, array('role'=>"group"));
$this->BsForm->setLeft(0);
$this->BsForm->setRight(12);
echo $this->BsForm->inputGroup('search_tree', array(array(
                                'content'=>'',
                                'id' => 'search_tree_button',
                                'icon'=>'glyphicon glyphicon-search',
                                'title' => __('Rechercher un service'),
                                'type' => 'button',
                                'state' => 'primary',
    ), array(
    'content'=>'<span class="caret"></span>',
                                'class' => 'dropdown-toggle',
                                'title' => __('Option de recherche'),
                                'data-toggle' => 'dropdown',
                                'icon'=>'glyphicon glyphicon-cog',
                                'after'=>'<ul class="dropdown-menu dropdown-menu-right" role="menu">
            <li><a id="search_tree_erase_button" title="Remettre à zéro la recherche">Effacer la recherche</a></li>
            <li class="divider"></li>
            <li><a id="search_tree_plier_button" title="Replier tous les services">Tout replier</i></a></li>
            <li><a id="search_tree_deplier_button" title="Déplier tous les services">Tout déplier</a></li>
        </ul>',
                                'type' => 'button',
                                'state' => 'default')
    ), array(
        'placeholder'=>__('Filtrer par nom de service'),//style="float: left; z-index: 2"
    ), array('multiple'=>true,'side'=>'right'));
echo $this->Bs->close();

echo $this->Bs->div('btn-group', null, array('role'=>"group"));
echo $this->Bs->btn('Nouveau', array('action' => 'add'), array(
        'escape' => false, 
        'title' => __('Nouveau service'),
        'icon'=>'glyphicon glyphicon-plus',
        'type'=>'primary',
        'id' => 'boutonAdd', 
        ));
echo $this->Bs->close();

echo $this->Bs->div('btn-group', null, array('role'=>"group"));
    echo $this->Bs->btn('Modifier', '#', 
            array('escape' => false, 
                'title' => __('Modifier le service'),
                'type'=>'primary',
                'icon'=>'glyphicon glyphicon-edit',
                'id' => 'boutonEdit'));
echo $this->Bs->close();

echo $this->Bs->div('btn-group', null, array('role'=>"group"));
    echo    $this->Bs->confirm($this->Bs->icon('copy').' Fusionner', 
            array('controller' => 'services', 'action' => 'fusionner'), 
            array('type' => 'success', 
                'title' => __('Fusionner le service'),
                'texte' => $this->BsForm->create(false, array(
                    'url' => array('controller' => 'services', 'action' => 'fusionner'),
                    'novalidate' => true))
                .$this->Bs->tag('p',__('Si vous ne souhaitez pas Fusionner un service, Fermer la boite de dialogue.'))
                .$this->Bs->tag('p',__('Avec quel service voullez-vous fusionner ?'))
                .$this->BsForm->select('Service.id', $services, array(
                'placeholder'=> __('Choisir un service'),
                'label' => false,
                'empty' => true, 
                'escape'=>false,    
                'class' => 'selectone'))
                .$this->BsForm->hidden('service_a_fusionner')
                .$this->BsForm->end()
                ,
                'header' => __('Vous allez fusionner un service !'),
                'style'=>array(
                    'border-bottom-right-radius'=> '4px',
                    'border-top-right-radius'=> '4px',
                ),
                'id' => 'boutonFusion',
            ), array('form'=>true)); 
echo $this->Bs->close();
    
echo $this->Bs->div('btn-group', null, array('role'=>"group"));
echo $this->Bs->btn('Supprimer', '#', array(
    'escape' => false, 
    'title' => __('Supprimer le service'),
    'type'=>'danger',
    'id' => 'boutonDelete', 
    'icon'=>'glyphicon glyphicon-trash',
    'confirm'=> __('Voulez-vous vraiment supprimer le service ?')
    ));
    
echo $this->Bs->close(2);
echo $this->Bs->tag('/br');
echo $this->Bs->div( null,$this->Tree->generateIndex($data, 'Service', 
         array('id' => 'id', 'display' => 'libelle', 'order' => 'order')), array('id'=>'arbre'));
?>
<script>
    function addAction() {
        window.location.href = $('a#boutonAdd').attr("href");
    }
    function editAction() {
        window.location.href = $('a#boutonEdit').attr("href");
    }
    function fusionService() {
        $('button#boutonFusion').click();
    }
    function deleteAction() {
        window.location.href = $('a#boutonDelete').attr("href");
    }
    $(document).ready(function () {
        var jstreeconf = {
            /* Initialisation de jstree sur la liste des services */
            "core": { //Paramétrage du coeur du plugin
                "multiple": false,
                "themes": { "stripes": true } //Une ligne sur deux est grise (meilleure lisibilité)
            },
            "checkbox": { //Paramétrage du plugin checkbox
                "three_state": false //Ne pas propager la séléction parent/enfants
            },
            "search": { //Paramétrage du plugin de recherche
                "fuzzy": false, //Indicates if the search should be fuzzy or not (should chnd3 match child node 3).
                "show_only_matches": true, //Masque les résultats ne correspondant pas
                "case_sensitive": false //Sensibilité à la casse
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
                "types",
                "contextmenu"
            ]
        };

        $('#arbre').jstree(jstreeconf);


        $("a#boutonEdit").hide();
        $("button#boutonFusion").hide();
        $("a#boutonDelete").hide();
        $('#arbre').on('changed.jstree', function (e, data) {
            /* Listener onChange qui fait la synchro jsTree/hiddenField */
            $("a#boutonEdit").hide().prop('href','#');
            $("button#boutonFusion").hide().prop('href','#');
            $("a#boutonDelete").hide().prop('href','#');
            if (data.selected.length) {
                var node = data.instance.get_node(data.selected);
                $("a#boutonEdit").show().prop('href', '/services/edit/' + data.instance.get_node(data.selected).data.id);
                $("button#boutonFusion").show().prop('href', '/services/fusion/' + data.instance.get_node(data.selected).data.id);
                $("#service_a_fusionner").val(data.instance.get_node(data.selected).data.id);
                if ($('#' + node.id).hasClass('deletable')) {
                    $("a#boutonDelete").show().prop('href', '/services/delete/' + data.instance.get_node(data.selected).data.id);
                }
            }
        });

        /* Recherche dans la liste jstree */
        $('#search_tree_button').click(function () {
            $('#arbre').jstree(true).search($('#search_tree').val());
        });

        $('#search_tree_erase_button').click(function () {
            $('#search_tree').val('');
            $('#search_tree_button').click();
        });

        $('#search_tree').keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                $('#search_tree_button').click();
                return false;
            }
        });
        $("#search_tree_plier_button").click(function () {
            $('#arbre').jstree('close_all');
        });
        $("#search_tree_deplier_button").click(function () {
            $('#arbre').jstree('open_all');
        });
    });

    function contextMenu(node) {
        // The default set of all items
        var items = {
            "add": {
                "label": "Nouveau",
                "action": addAction,
                "icon": "fa fa-plus",
            },
            "edit": {
                "label": "Modifier",
                "action": editAction,
                "icon": "fa fa-edit"
            },
            "delete": {
                "label": "Supprimer",
                "action": deleteAction,
                "icon": "fa fa-trash-o"
                
            },
            "fusion": {
                "label": "Fusionner",
                "action": fusionService,
                "icon": "fa fa-copy",
                "separator_before": true
                
            }
        };
        if ($('#'+node.id).hasClass("deletable") == false) {
            delete items.delete;
        }
        return items;
    }
</script>