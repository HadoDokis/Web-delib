<?php
$this->Html->addCrumb('Liste des thèmes');

echo $this->Bs->tag('h3', 'Liste des thèmes');

echo $this->Bs->div('btn-toolbar', null, array('role'=>"toolbar"));
echo $this->Bs->div('btn-group', null, array('role'=>"group"));
$this->BsForm->setLeft(0);
$this->BsForm->setRight(12);
echo $this->BsForm->inputGroup('search_tree', array(array(
                                'content'=>'',
                                'id' => 'search_tree_button',
                                'icon'=>'glyphicon glyphicon-search',
                                'type' => 'button',
                                'state' => 'primary',
    ), array(
    'content'=>'<span class="caret"></span>',
                                'class' => 'dropdown-toggle',
                                'data-toggle' => 'dropdown',
                                'icon'=>'glyphicon glyphicon-cog',
                                'after'=>'<ul class="dropdown-menu dropdown-menu-right" role="menu">
            <li><a id="search_tree_erase_button" title="Remettre à zéro la recherche">Effacer la recherche</a></li>
            <li class="divider"></li>
            <li><a id="search_tree_plier_button" title="Replier tous les thèmes">Tout replier</i></a></li>
            <li><a id="search_tree_deplier_button" title="Déplier tous les thèmes">Tout déplier</a></li>
        </ul>',
                                'type' => 'button',
                                'state' => 'default')
    ), array(
        'placeholder'=>__('Filtrer par nom de thème'),//style="float: left; z-index: 2"
    ), array('multiple'=>true,'side'=>'right'));
echo $this->Bs->close();

echo $this->Bs->div('btn-group', null, array('role'=>"group"));
echo $this->Bs->btn('Nouveau', array('action' => 'add'), array(
        'escape' => false, 
        'icon'=>'glyphicon glyphicon-plus',
        'type'=>'primary',
        'id' => 'boutonAdd', 
        ));
echo $this->Bs->close();

echo $this->Bs->div('btn-group', null, array('role'=>"group"));
    echo $this->Bs->btn('Modifier', '#', 
            array('escape' => false, 
                'type'=>'warning',
                'icon'=>'glyphicon glyphicon-edit',
                'id' => 'boutonEdit'));
    echo $this->Bs->close();
    
    echo $this->Bs->div('btn-group', null, array('role'=>"group"));
    echo $this->Bs->btn('Supprimer', '#', array(
        'escape' => false, 
        'type'=>'danger',
        'id' => 'boutonDelete', 
        'icon'=>'glyphicon glyphicon-trash',
        'confirm'=> __('Voulez-vous vraiment supprimer le thème ?')
        ));
    
echo $this->Bs->close(2);
echo $this->Bs->tag('/br');
echo $this->Bs->div( null,$this->Tree->generateIndex($data, 'Theme', 
         array('id' => 'id', 'display' => 'libelle', 'order' => 'order')), array('id'=>'arbre'));
?>
<script>
    function addAction() {
        window.location.href = $('a#boutonAdd').attr('href');
    }
    function editAction() {
        window.location.href = $('a#boutonEdit').attr('href');
    }
    function deleteAction() {
        window.location.href = $('a#boutonDelete').attr('href');
    }
    $(document).ready(function () {
        var jstreeconf = {
            /* Initialisation de jstree sur la liste des thèmes */
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
                "types", //Pour les icônes
                "contextmenu" //Menu clic droit
            ]
        };

        $('#arbre').jstree(jstreeconf);

        $("a#boutonEdit").hide();
        $("a#boutonDelete").hide();
        $('#arbre').on('changed.jstree', function (e, data) {
            /* Listener onChange qui fait la synchro jsTree/hiddenField */
            $("a#boutonEdit").hide().prop('href', '#');
            $("a#boutonDelete").hide().prop('href', '#');
            if (data.selected.length) {
                var node = data.instance.get_node(data.selected);
                $("a#boutonEdit").show().prop('href', '/admin/themes/edit/' + data.instance.get_node(data.selected).data.id)
                if ($('#' + node.id).hasClass('jstree-leaf')) { //Ne peux supprimer que les "feuilles"
                    $("a#boutonDelete").show().prop('href', '/admin/themes/delete/' + data.instance.get_node(data.selected).data.id)
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
                "separator_after": true
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
            }
        };
        if ($('#' + node.id).hasClass("jstree-leaf") == false) {
            delete items.delete;
        }
        return items;
    }
</script>