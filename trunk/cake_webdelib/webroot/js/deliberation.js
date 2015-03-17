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
                $.jGrowl("Format du document invalide. Seuls les fichiers au format ODT sont autorisés.", {header: "<strong>Erreur :</strong>"});
                $(this).val(null);
                return false;
            }
            //Test sur le nom de fichier (>75car)
            var tmpArray = $(this).val().split('\\');
            var filename = tmpArray[tmpArray.length - 1];
            if (filename.length > 75) {
                $.jGrowl("Le nom du fichier ne doit pas dépasser 75 caractères.", {header: "<strong>Erreur :</strong>"});
                $(this).val(null);
                return false;
            }
        }
    });
});

function disableExitWarning() {
    $(window).unbind('beforeunload');
    objMenuTimeout = setTimeout(function () {
        onUnloadEditForm();
    }, 2000); // 2000 millisecondes = 2 secondes
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
                $("#SeanceSeance").val(seancesSelected);
                $("#SeanceSeance").select2("val", seancesSelected);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(textStatus);
        }
    });
}

// variables globales
var nbAnnexeAAjouter = 0;

/**
 * Fonction d'ajout d'une nouvelle annexe : appelée a l'enregistrement de la modale
 * @returns {boolean}
 */
function ajouterAnnexe() {
    if ($('#Annex0File').val() == '') {
        $.jGrowl('Impossible d\'ajouter l\'annexe, aucun fichier selectionné !', {header: "<strong>Erreur :</strong>"});
        $('#annexeModal #annexe-error-message').show();
        setTimeout(function () {
            $('#annexeModal #annexe-error-message').hide();
        }, 5000);
        return false;
    }
    nbAnnexeAAjouter++;
    var ref = $('#annexeModal input#refDelib').val(),
        $annexTable = $('table#tableAnnexes' + ref + ' > tbody:last'),
        $addDiv = $('#ajouteAnnexes' + ref);

    //Copie du formulaire vers un bloc caché
    var formulaire = $('.modal-body #annexeModalBloc').appendTo($addDiv);
    //Remise en place formulaire modal
    var $annexeModalBloc = formulaire.clone(true);
    $('#annexeModal .modal-body').append($annexeModalBloc);

    //Changement d'attributs
    formulaire.attr('id', 'addAnnexe' + nbAnnexeAAjouter + ref);
    //Change les attributs id et name avec le numéro courant
    $addDiv.find('input').each(function () {
        if ($(this).attr('id') !== undefined)
            $(this).attr('id', $(this).attr('id').replace('0', nbAnnexeAAjouter));
        $(this).attr('name', $(this).attr('name').replace('0', nbAnnexeAAjouter));
    });
    //Change la cible des labels avec le numéro courant
    $addDiv.find('label').each(function () {
        if ($(this).attr('for') !== undefined)
            $(this).attr('for', $(this).attr('for').replace('0', nbAnnexeAAjouter));
    });

    //Récupération des valeurs
    var filename = $('#Annex' + nbAnnexeAAjouter + 'File').val().split("\\").pop(),
        titre = $('#Annex' + nbAnnexeAAjouter + 'Titre').val(),
        joindre_ctrl = $('#Annex' + nbAnnexeAAjouter + 'Ctrl').prop('checked'),
        joindre_fusion = $('#Annex' + nbAnnexeAAjouter + 'Fusion').prop('checked'),
        $line = $('<tr class="success" title="Annexe à ajouter" id="ligneAnnexe' + nbAnnexeAAjouter + ref + '"></tr>');

    //Pour affichage
    if (joindre_ctrl) joindre_ctrl = 'Oui';
    else joindre_ctrl = 'Non';

    if (joindre_fusion) joindre_fusion = 'Oui';
    else joindre_fusion = 'Non';

    var numeroligne = $('table#tableAnnexes' + ref + ' > tbody tr').length + 1;
    //Ajout de la ligne dans le tableau des annexes
    $line.append("<td>" + numeroligne + "</td>");
    $line.append("<td>" + filename + "</td>");
    $line.append("<td>" + titre + "</td>");
    $line.append("<td>" + joindre_ctrl + "</td>");
    $line.append("<td>" + joindre_fusion + "</td>");

    //Boutons d'action sur la nouvelle annexe
    var action = '<div class="btn-group">' +
        '<a href="#tableAnnexes' + ref + '" id="annulerAjoutAnnexe' + nbAnnexeAAjouter + ref + '" class="btn btn-warning btn-mini annulerAjoutAnnexe" data-ref="' + ref + '" data-annexeId="' + nbAnnexeAAjouter + '" title="Annuler l\'ajout de cette annexe"><i class="fa fa-undo"></i> Annuler</a>' +
        '</div>';

    $line.append("<td style='text-align: center;'>" + action + "</td>");

    $line.appendTo($annexTable);

    //Déclencheurs des boutons
    $('#annulerAjoutAnnexe' + nbAnnexeAAjouter + ref).click(function () {
        annulerAjoutAnnexe(this);
    });

    closeAnnexeModal();

    $('#tableAnnexes' + ref).slideDown('slow');

    $('html, body').animate({
        scrollTop: $('#tableAnnexes' + ref).offset().top - 42 // Prise en compte de la topbar
    }, 'slow');

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
    return false;
}

/**
 * Action déclenchée lors du clic sur le bouton "Ajouter une annexe"
 * @param element reference du lien cliqué (gestion du multi-délib)
 */
function afficherAnnexeModal(element) {
    console.log('toto');
    $('#annexeModal #refDelib').val($(element).attr('data-ref'));
    if ($(element).attr('data-annexeid') != '')
        $('#annexeModal #numAnnexe').val($(element).attr('data-annexeId'));
    $('#Annex0Titre').val('');
    $("#annexeModal").find('input').prop('disabled', false);
    $("#annexeModal").modal('show');
    //Déclencheurs des boutons
    $('#annexeModalSubmit').unbind('click');
    $('#annexeModalSubmit').click(function () {
        ajouterAnnexe();
        return false;
    });
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
    $('#Annex0Ctrl').prop('checked', false);
    $('#Annex0Fusion').prop('checked', true);
    $("#annexeModal").find('input').prop('disabled', true);
    //RAZ affichage
    $('#Annex0Ctrl').closest('div').show();
    $('#Annex0Fusion').closest('div').show();
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

// Fonction de modification de l'annexe
function modifierAnnexe(annexeId) {
    var $bloc = $('#editAnnexe' + annexeId);

    //Activation conditionnelle des checkboxes fusion et ctrl_legalite selon extension annexe
    /*var fileext = $bloc.find('.annexefilename').text().split('.').pop().toLowerCase();
    if ($.inArray(fileext, extensionsFusion) === -1) {
        $bloc.find('#modifieAnnexeFusion' + annexeId).prop('checked', false).prop('disabled', true);
    } else {
        $bloc.find('#modifieAnnexeFusion' + annexeId).prop('disabled', false);
    }
    if ($.inArray(fileext, extensionsCtrl) === -1) {
        $bloc.find('#modifieAnnexeCtrl' + annexeId).prop('checked', false).prop('disabled', true);
    } else {
        $bloc.find('#modifieAnnexeCtrl' + annexeId).prop('disabled', false);
    }

    $bloc.find('#urlWebdavAnnexe' + annexeId).show();*/

    $bloc.addClass('warning').addClass('aModifier').attr('title', 'Annexe à modifier');

   /* $bloc.find('.annexe-edit').each(function () {
       // $(this).removeAttr('disabled');
        $(this).show();
    });*/
    $bloc.find('.annexe-view').hide();
    $bloc.find('.annexe-edit-btn').hide();
    $bloc.find('.annexe-edit').show();
    $bloc.find('.annexe-cancel-btn').show();
}

// Fonction d'annulation de la modification de l'annexe
function annulerModifierAnnexe(annexeId) {
    var $bloc = $('#editAnnexe' + annexeId);
    $bloc.find('#modifieAnnexeTitre' + annexeId).val($bloc.find('#afficheAnnexeTitre' + annexeId).attr('data-valeurinit'));
    $bloc.find('#modifieAnnexeCtrl' + annexeId).prop('checked', $bloc.find('#afficheAnnexeCtrl' + annexeId).attr('data-valeurinit') == 1);
    $bloc.find('#modifieAnnexeFusion' + annexeId).prop('checked', $bloc.find('#afficheAnnexeFusion' + annexeId).attr('data-valeurinit') == 1);
   
    $bloc.removeClass('warning').removeClass('aModifier').removeAttr('title');
    
//    $bloc.find('.annexe-cancel').each(function () {
//       // $(this).prop('disabled', true);
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

//Gestion des sorties du formulaire
function onUnloadEditForm() {
    $(window).bind('beforeunload', function () {
        return "Attention !\n\n Si vous quittez cette page vous allez perdre vos modifications.";
    });
}

