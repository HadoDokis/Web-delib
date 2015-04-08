$(document).ready(function () {
    onUnloadEditForm();
   /* var file_input_index = 0;
    $('.file-texte').each(function () {
        file_input_index++;
        $(this).wrap('<div id="file_input_container_' + file_input_index + '"></div>');
        $(this).after('<a href="javascript:void(0)" class="purge_file btn btn-mini btn-danger"  onclick="resetUpload(\'file_input_container_' + file_input_index + '\')"><i class="fa fa-eraser"></i> Effacer</a>');
    });*/

    $("#DeliberationIsMultidelib").change();

    //Déclenché lors de la fermeture de la modale
    $('#annexeModal').on('hidden', function () {
        resetAnnexeModal();
    });
    
    
/**
 * Action déclenchée lors du clic sur le bouton "Ajouter une annexe"
 * @param element reference du lien cliqué (gestion du multi-délib)
 */
    $('#btnAnnexeModal').on('click', function () {
        
        $('#annexeModal #refDelib').val($(this).attr('data-ref'));
        
        resetAnnexeModal();
        $("#annexeModal").modal('show');
        disableAnnexeModal(false);
        //Déclencheurs des boutons
        $('#btnAnnexeModalAdd').unbind('click');
        $('#btnAnnexeModalAdd').on('click', function () {
            ajouterAnnexe();
        });
        
        //On remet les champs de la popup en disabled lorsque l'utilisateur clique sur fermer pour eviter leurs Post
        $('#annexeModal .btn-link').on('click', function () {
            disableAnnexeModal(true);
        });
    
        return false;
    });
 
    $("#DeliberationEditForm, #DeliberationAddForm").submit(function () {
        $(window).unbind("beforeunload");
    });

    $("form#DeliberationAddForm a").on('click', disableExitWarning);
    $("form#DeliberationEditForm a").on('click', disableExitWarning);

    $('.file-texte').change(function () {
        if ($(this).val() != '') {
            var tmpArray = $(this).val().split('.');
            //Test sur l'extension (ODT ?)
            var extension = tmpArray[tmpArray.length - 1];
            if (extension.toLowerCase() != 'odt') {
                $.growl( 
                {
                    title: "<strong>Erreur :</strong>",
                    message: 'Format du document invalide. Seuls les fichiers au format ODT sont autorisés.'
                },{type:"danger"});
                $(this).val(null);
                return false;
            }
            //Test sur le nom de fichier (>75car)
            var tmpArray = $(this).val().split('\\');
            var filename = tmpArray[tmpArray.length - 1];
            if (filename.length > 75) {
                $.growl( 
                {
                    title: "<strong>Erreur :</strong>",
                    message: 'Le nom du fichier ne doit pas dépasser 75 caractères.'
                },{type:"danger"});
                $(this).val(null);
                return false;
            }
        }
    });
    
    //modifier l'ordre des annnexes 
    $('#tableAnnexesdelibPrincipale .selectone').change(function(){
        var ref = $('#refProjet').val();
        var tableAnnexes = $('table#tableAnnexes' + ref);
        //on prends l'id de l'annexe dont on souhaite changer la position
        //et celui de destination, ainsi que leur position initiale respective
        var annexe_src_id = $(this).closest('tr').attr('data-annexeid');
        var annexe_src_pos = $(this).closest('tr').attr('data-position');
        var annexe_dest_id = tableAnnexes.find('[data-position=' + $( this ).val() + ']').attr('data-annexeid');
        var annexe_dest_pos = $( this ).val();
        switchOrdrePosition(annexe_src_id, annexe_src_pos, annexe_dest_id, annexe_dest_pos);
    });
});

//on switch la position de l'annexe avec celle de destination
function switchOrdrePosition(annexe_src_id, annexe_src_pos, annexe_dest_id, annexe_dest_pos)
{
    var ref = $('#refProjet').val();
    var annexe_destination = $('table#tableAnnexes' + ref + ' #editAnnexe' + annexe_dest_id);
    var annexe_source = $('table#tableAnnexes' + ref + ' #editAnnexe' + annexe_src_id);
    //on switch la position de l'annexe de destination avec celui d'origine
    annexe_destination.attr('data-position', annexe_src_pos);
    annexe_source.attr('data-position', annexe_dest_pos);

    //on met a jour les positions de sources et de destinations des text et selected
    annexe_destination.find('.selectone').select2("val", annexe_src_pos);
    annexe_destination.find('.annexe-view:first').html( annexe_src_pos );
    annexe_source.find('.selectone').select2("val", annexe_dest_pos);
    annexe_source.find('.annexe-view:first').html( annexe_dest_pos );

    //on met a jour l'ordre d'affichage
    ordonnerPosition($('table#tableAnnexes' + ref), 'data-position');
}
    
//modifie l'ordre d'affichage des annnexes une fois leurs positions afféctées
function ordonnerPosition(table, type){
    var rows = table.find('tr').get();
    rows.sort(function(a, b) {
        var keyA = parseInt($(a).attr(type));
        var keyB = parseInt($(b).attr(type));
        if (keyA < keyB) return -1;
        if (keyA > keyB) return 1;
        return 0;
    });
    $.each(rows, function(index, row) {
        table.children('tbody').append(row);
    });
}

function disableExitWarning() {
    $(window).unbind('beforeunload');
    objMenuTimeout = setTimeout(function () {
        onUnloadEditForm();
    }, 2000); // 2000 millisecondes = 2 secondes
}

//Gestion des sorties du formulaire
function onUnloadEditForm() {
    $(window).bind('beforeunload', function () {
        return "Attention !\n\n Si vous quittez cette page vous allez perdre vos modifications.";
    });
}

function updateTypeseances(domObj) {
    var ajaxUrl = '/deliberations/getTypeseancesParTypeacteAjax/' + $(domObj).val();
    $.ajax({
        url: ajaxUrl,
        beforeSend: function () {
            $('#selectTypeseances').html('');
            $('#selectDatesSeances').html('');
        },
        success: function (result) {
            $('#selectTypeseances').html(result);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(textStatus);
        }
    });
}

function updateDatesSeances(domObj) {
    var ajaxUrl = '/deliberations/getSeancesParTypeseanceAjax/' + $(domObj).val();
    var seancesSelected = $("#SeanceSeance").val();
    $.ajax({
        url: ajaxUrl,
        beforeSend: function () {
            $('#selectDatesSeances').html('');
        },
        success: function (result) {
            $('#selectDatesSeances').html(result);
            if (seancesSelected != null) {
                alert(seancesSelected);
                $("#Deliberationseance").val(seancesSelected);
                $("#Deliberationseance").select2("val", seancesSelected);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(textStatus);
        }
    });
}

/**
 * Fonction d'ajout d'une nouvelle annexe : appelée a l'enregistrement de la modale
 * @returns {boolean}
 */
function ajouterAnnexe() {
    if ($('#Annex0File').val() == '') {
        $.growl( 
        {
            title: "<strong>Erreur :</strong>",
            message: 'Impossible d\'ajouter l\'annexe, aucun fichier selectionné !'
        },{type:"danger"});
        
        $("#annexeModal").modal('hide');
        /*$('#annexeModal #annexe-error-message').show();
        setTimeout(function () {
            $('#annexeModal #annexe-error-message').hide();
        }, 5000);*/
        //on reset le modal 
        resetAnnexeModal();
        //on remet les champs en disabled pour eviter leurs POST au submit global
        disableAnnexeModal(true);
        return false;
    }
    
    
    var ref = $('#annexeModal input#refDelib').val();
    var annexTable = $('table#tableAnnexes' + ref + ' > tbody:last');
    var numAnnexeAAjouter = $('table#tableAnnexes' + ref + ' tr').length;
    
    $('#annexeModal input#numAnnexe').val(numAnnexeAAjouter);
    //ajouteAnnexesdelibPrincipale
    //Copie du formulaire vers un bloc caché
    
    //Remise en place formulaire modal
    //var $annexeModalBloc = formulaire.clone(true);
    //$('#annexeModal .modal-body').append($annexeModalBloc);

    //Changement d'attributs
    //formulaire.attr('id', 'addAnnexe' + numAnnexeAAjouter + ref);
    //Change les attributs id et name avec le numéro courant
    $('#annexeModal').find('input').each(function () {
        if ($(this).attr('id') !== undefined){
        $(this).attr('id', $(this).attr('id').replace('0', numAnnexeAAjouter));
        }
        if ($(this).attr('name') !== undefined){
        $(this).attr('name', $(this).attr('name').replace('0', numAnnexeAAjouter));
        }
    });
    //Change la cible des labels avec le numéro courant
    /*$('#annexeModal').find('label').each(function () {
        if ($(this).attr('for') !== undefined)
            $(this).attr('for', $(this).attr('for').replace('0', numAnnexeAAjouter));
    });*/

    //Récupération des valeurs
    var joindre_ctrl = $('#Annex' + numAnnexeAAjouter + 'Ctrl').prop('checked');
    var joindre_fusion = $('#Annex' + numAnnexeAAjouter + 'Fusion').prop('checked');
    var line = $('<tr class="success" title="Annexe à ajouter" id="ligneAnnexe' + numAnnexeAAjouter + ref + '"></tr>');

    //Pour affichage
    if (joindre_ctrl) joindre_ctrl = 'Oui';
    else joindre_ctrl = 'Non';

    if (joindre_fusion) joindre_fusion = 'Oui';
    else joindre_fusion = 'Non';
    
    //Ajout de la ligne dans le tableau des annexes
    line.append("<td>" + numAnnexeAAjouter + "</td>");
    line.append("<td>" + $('#Annex' + numAnnexeAAjouter + 'File').val().split("\\").pop() + "</td>");
    line.append("<td>" + $('#Annex' + numAnnexeAAjouter + 'Titre').val() + "</td>");
    line.append("<td>" + joindre_ctrl + "</td>");
    line.append("<td>" + joindre_fusion + "</td>");

    //Boutons d'action sur la nouvelle annexe
    var action = '<div class="btn-group">' +
        '<a href="#tableAnnexes' + ref + '" id="annulerAjoutAnnexe' + numAnnexeAAjouter + ref + '" class="btn btn-warning btn-mini annulerAjoutAnnexe" data-ref="' + ref + '" data-annexeId="' + numAnnexeAAjouter + '" title="Annuler l\'ajout de cette annexe"><i class="fa fa-undo"></i> Annuler</a>' +
        '</div>';

    line.append("<td style='text-align: center;'>" + action + "</td>");

    line.appendTo(annexTable);
    
    //Copie des données pour le formulaire
    $('#annexeModal .modal-body ').clone().appendTo($('#ajouteAnnexes' + ref)).addClass('annexeAjoutee' + numAnnexeAAjouter);
    
    $('#annexeModal').find('input').each(function () {
        if ($(this).attr('id') !== undefined){
        $(this).attr('id', $(this).attr('id').replace(numAnnexeAAjouter, '0'));
        }
        if ($(this).attr('name') !== undefined){
        $(this).attr('name', $(this).attr('name').replace(numAnnexeAAjouter,'0'));
        }
    });

    //Déclencheurs des boutons
    $('#annulerAjoutAnnexe' + numAnnexeAAjouter + ref).click(function () {
        annulerAjoutAnnexe(this);
    });

    closeAnnexeModal();

    $('#tableAnnexes' + ref).slideDown('slow');

    $('html, body').animate({
        scrollTop: $('#tableAnnexes' + ref).offset().top - 42 // Prise en compte de la topbar
    }, 'slow');
    
    //on reset le modal 
    resetAnnexeModal();
    //on remet les champs en disabled pour eviter leurs POST au submit global
    disableAnnexeModal(true);
    return false;
}

/**
 * Annule l'ajout d'une annexe (supprime du tableau et du formulaire)
 * @param element
 * @returns {boolean}
 */
function annulerAjoutAnnexe(element) {
    var annexeId = $(element).attr('data-annexeId'),
        ref = $(element).attr('data-ref');

    $('#addAnnexe' + annexeId + ref).remove();
    $('#ligneAnnexe' + annexeId + ref).fadeOut(300, function () {
        if ($('#tableAnnexes' + ref + ' tbody tr:visible').length == 0)
            $('#tableAnnexes' + ref).hide();
    });
    
    //supprimer l'enregistrement
    var ref = $('#annexeModal input#refDelib').val();
    $('#ajouteAnnexes' + ref + ' .annexeAjoutee' + annexeId).remove();
    return false;
}

/**
 * Ferme la fenêtre modale d'ajout d'annexe
 */
function closeAnnexeModal() {
    //Masquer la modale
    $("#annexeModal").modal('hide');
    resetAnnexeModal();
}

/**
 * remet les champs à zéro
 */
function resetAnnexeModal() {
    //RAZ titre
    $('#annexeModalTitle').text("Nouvelle annexe");
    $('#annexeModal .error-message').remove();
    //RAZ des champs
    $('#Annex0Titre').val(null);
    $('#Annex0File').val(null);
    $('#Annex0File').filestyle('clear');
    $('#Annex0Ctrl').prop('checked', false);
    $('#Annex0Fusion').prop('checked', true);
    $("#annexeModal").find('input').prop('disabled', 'disabled');
    //RAZ affichage
    $('#Annex0Ctrl').closest('div').show();
    $('#Annex0Fusion').closest('div').show();
}

/**
 * desactive/active les champs (desactive : pour eviter leur POST)
 */
function disableAnnexeModal(disabled) {
    if(disabled==true) disabled='disabled';
    $('#Annex0Titre').prop('disabled', disabled);
    $('#Annex0File').prop('disabled', disabled);
    $('#Annex0Ctrl').prop('disabled', disabled);
    $('#Annex0Fusion').prop('disabled', disabled);
}

// Fonction de suppression d'une annexe
function supprimerAnnexe(annexeId) {
    var $bloc = $('#editAnnexe' + annexeId);
    var ref = $bloc.attr('data-ref');
    $bloc.addClass('danger').addClass('aSupprimer').attr('title', 'Annexe à supprimer');
    var supAnnexe = $(document.createElement('input')).attr({
        id: 'supprimeAnnexe' + annexeId,
        name: 'data[AnnexesASupprimer][' + annexeId + ']',
        type: 'hidden', value: annexeId});

    $('#supprimeAnnexes' + ref).append(supAnnexe);
    $bloc.find('.annexe-edit-btn').hide();
    $bloc.find('.annexe-delete-btn').show();
}

// Fonction de d'annulation de suppression d'une annexe
function annulerSupprimerAnnexe(annexeId) {
    var $bloc = $('#editAnnexe' + annexeId);
    $bloc.removeClass('danger').removeClass('aSupprimer').removeAttr('title');
    $('#supprimeAnnexe' + annexeId).remove();
    $bloc.find('.annexe-delete-btn').hide();
    $bloc.find('.annexe-edit-btn').show();
}



// Fonction d'annulation de la modification de l'annexe
function annulerModifierAnnexe(annexeId) {
    var $bloc = $('#editAnnexe' + annexeId);
    $bloc.find('#modifieAnnexeTitre' + annexeId).val($bloc.find('#afficheAnnexeTitre' + annexeId).attr('data-valeurinit'));
    $bloc.find('#modifieAnnexeCtrl' + annexeId).prop('checked', $bloc.find('#afficheAnnexeCtrl' + annexeId).attr('data-valeurinit'));
    $bloc.find('#modifieAnnexeFusion' + annexeId).prop('checked', $bloc.find('#afficheAnnexeFusion' + annexeId).attr('data-valeurinit'));

    //on remet la position par defaut de l'annexe en le switchant de la même maniere qu'au changement de position
    //on prends l'id de l'annexe dont on souhaite changer la position
    //et celui de destination, ainsi que leur position initiale respective
    var ref = $('#refProjet').val();
    var tableAnnexes = $('table#tableAnnexes' + ref);
    
    var annexe_src_id = annexeId;
    var annexe_src_pos = $('#editAnnexe'+ annexeId).attr('data-position');
    var annexe_dest_id = tableAnnexes.find('[data-position=' +  $('#editAnnexe'+ annexeId).attr('data-valeurinit') + ']').attr('data-annexeid');
    var annexe_dest_pos = tableAnnexes.find('[data-position=' + $('#editAnnexe'+ annexeId).attr('data-valeurinit')+ ']').attr('data-position');//$( this ).val();   
    switchOrdrePosition(annexe_src_id, annexe_src_pos, annexe_dest_id, annexe_dest_pos);

    $bloc.removeClass('warning').removeClass('aModifier').removeAttr('title');
    
    //on remet les champs en disabled
    $bloc.find('#modifieAnnexeFusion' + annexeId).prop('disabled', 'disabled');
    $bloc.find('#modifieAnnexeCtrl' + annexeId).prop('disabled', 'disabled');
    $bloc.find('#modifieAnnexeTitre' + annexeId).prop('disabled', 'disabled');
    $bloc.find('#modifieAnnexePosition' + annexeId).prop('disabled', 'disabled');
    
//    $bloc.find('.annexe-cancel').each(function () {
//       // $(this).prop('disabled', 'disabled');
//        $(this).hide();
//    });

    $bloc.find('.annexe-edit').hide();
    $bloc.find('.annexe-cancel-btn').hide();
    $bloc.find('.annexe-view').show();
    $bloc.find('.annexe-edit-btn').show();
    
}

// affichage de l'éditeur de texte intégré ckEditor
function editerTexte(obj, textId, afficheTextId) {
    $('#' + textId).ckeditor();
    $('#' + afficheTextId).hide();
    $(obj).hide();
}

function reset_html(id) {
    $('#' + id + ' input[type=file]').val(null);
    $('#' + id + ' a').remove();
}

// Fonction de modification de l'annexe
function modifierAnnexe(annexeId) {
    var $bloc = $('#editAnnexe' + annexeId);

    //Activation conditionnelle des checkboxes fusion et ctrl_legalite selon extension annexe
    var fileext = $bloc.find('.annexefilename').text().split('.').pop().toLowerCase();
    if ($.inArray(fileext, extensionsFusion) === -1) {
        $bloc.find('#modifieAnnexeFusion' + annexeId).prop('checked', false).prop('disabled', 'disabled');
    } else {
        $bloc.find('#modifieAnnexeFusion' + annexeId).prop('disabled', false);
    }
    if ($.inArray(fileext, extensionsCtrl) === -1) {
        $bloc.find('#modifieAnnexeCtrl' + annexeId).prop('checked', false).prop('disabled', 'disabled');
    } else {
        $bloc.find('#modifieAnnexeCtrl' + annexeId).prop('disabled', false);
    }
    $bloc.find('#modifieAnnexeTitre' + annexeId).prop('disabled', false);
    $bloc.find('#modifieAnnexePosition' + annexeId).prop('disabled', false);
    
    $bloc.find('#urlWebdavAnnexe' + annexeId).show();

    $bloc.addClass('warning').addClass('aModifier').attr('title', 'Annexe à modifier');

    $bloc.find('.annexe-edit').each(function () {
       // $(this).removeAttr('disabled');
        $(this).show();
    });
    $bloc.find('.annexe-view').hide();
    $bloc.find('.annexe-edit-btn').hide();
    $bloc.find('.annexe-edit').show();
    $bloc.find('.annexe-cancel-btn').show();
}