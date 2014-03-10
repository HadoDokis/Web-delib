$(document).ready(function () {
    $("#DeliberationIsMultidelib").change(function () {
        if ($(this).prop("checked")) {
            $('#lienTab5').show();
            $('#htextedelib').hide();
            $('#lienTab3').hide();
            $('#delibPrincipaleAnnexeRatt').append($('#DelibPrincipaleAnnexes').detach());
            $('#texteDelibOngletDelib').append($('#texteDeliberation').detach());
        }
        else {
            $('#lienTab3').show();
            $('#lienTab5').hide();
            $('#htextedelib').show();
            $('#DelibOngletAnnexes').append($('#DelibPrincipaleAnnexes').detach());
            $('#texteDelibOngletTextes').append($('#texteDeliberation').detach());
        }
    }).change();
});

// variables globales
var iMultiDelibAAjouter = 1000;

// Fonction d'ajout d'une nouvelle deliberation : duplique le div ajouteMultiDelibTemplate et incrémente l'indexe
function ajouterMultiDelib() {
    iMultiDelibAAjouter++;
    //Clone le bloc template
    var $newDelibBloc = $('#delibRattacheeTemplate').clone(true);

    //Change attributs id et name
    $newDelibBloc.attr('id', $newDelibBloc.attr('id').replace('Template', iMultiDelibAAjouter));

    var $libelle = $newDelibBloc.find('textarea#MultidelibTemplateObjetDelib');

    $libelle.prop('disabled', false)
        .attr('id', $libelle.attr('id').replace('Template', iMultiDelibAAjouter))
        .attr('name', $libelle.attr('name').replace('Template', iMultiDelibAAjouter));

    var $texte = $newDelibBloc.find('input#MultidelibTemplateDeliberation');
    $texte
        .attr('id', $texte.attr('id').replace('Template', iMultiDelibAAjouter))
        .attr('name', $texte.attr('name').replace('Template', iMultiDelibAAjouter));

    var $gabarit = $newDelibBloc.find('input#MultidelibTemplateGabarit');
    $gabarit
        .attr('id', $gabarit.attr('id').replace('Template', iMultiDelibAAjouter))
        .attr('name', $gabarit.attr('name').replace('Template', iMultiDelibAAjouter));

    var $gabaritBloc = $newDelibBloc.find('#MultidelibTemplateGabaritBloc');
    $gabaritBloc.attr('id', $gabaritBloc.attr('id').replace('Template', iMultiDelibAAjouter));

    var $supprimerGabarit = $gabaritBloc.find('#supprimerMultidelibTemplateGabarit');
    $supprimerGabarit.attr('id', $supprimerGabarit.attr('id').replace('Template', iMultiDelibAAjouter));
    $supprimerGabarit.attr('onclick', $supprimerGabarit.attr('onclick').replace('Template', iMultiDelibAAjouter));

    if (current_gabarit_name != undefined) {
        $texte.hide();
        $gabaritBloc.find('.gabarit_name_multidelib').text(current_gabarit_name);
        $gabarit.prop('disabled', false).val('1');
    } else {
        $gabaritBloc.hide();
        $texte.prop('disabled', false);
    }


    $newDelibBloc.find('#ajouteAnnexesRef').attr('id', 'ajouteAnnexesdelibRattachee' + iMultiDelibAAjouter);

    $newDelibBloc.find('#tableAnnexesdelibRattacheeTemplate').attr('id', 'tableAnnexesdelibRattachee' + iMultiDelibAAjouter);
    $newDelibBloc.find('#supprimeAnnexesdelibRattacheeTemplate').attr('id', 'supprimeAnnexesdelibRattachee' + iMultiDelibAAjouter);
    $newDelibBloc.find('#ajouteAnnexesdelibRattacheeTemplate').attr('id', 'ajouteAnnexesdelibRattachee' + iMultiDelibAAjouter);

    $newDelibBloc.find('#annexeModalAddLinkdelibRattacheeTemplate')
        .attr('id', 'annexeModalAddLinkdelibRattachee' + iMultiDelibAAjouter)
        .attr('data-ref', 'delibRattachee' + iMultiDelibAAjouter);

    $('#ajouteMultiDelib').append($newDelibBloc);
    $newDelibBloc.fadeIn(300);
}

function supprimerGabaritMultidelib(delibId){
    $('input#Multidelib' + delibId + 'Deliberation').prop('disabled', false).show();
    $('#Multidelib'+delibId+'GabaritBloc').remove();
}

// Fonction de modification d'une délibération rattachée
function modifierDelibRattachee(delibId) {
    var $bloc = $('#delibRattachee' + delibId);
    $bloc.addClass('aModifier');
    $bloc.find('legend span.label').addClass('label-warning').text('Modification');

    $bloc.find('#delibRattacheeDisplay' + delibId).hide();
    $bloc.find('#delibRattacheeForm' + delibId).show();

    $bloc.find('#Multidelib' + delibId + 'Id').prop('disabled', false);
    $bloc.find('#Multidelib' + delibId + 'ObjetDelib').prop('disabled', false);
    $bloc.find('#Multidelib' + delibId + 'Deliberation').prop('disabled', false);

    $('#Multidelib' + delibId + 'ObjetDelib').val($('#Multidelib' + delibId + 'libelle').text());

    $bloc.find('.actions-multidelib').hide();
    $bloc.find('#annulerModifierDelibRattachee' + delibId).show();
}

// Fonction d'annulation des modifications d'une délibération rattachée
function annulerModifierDelibRattachee(delibId) {
    var $bloc = $('#delibRattachee' + delibId);

    $bloc.find('#delibRattacheeDisplay' + delibId).show();
    $bloc.find('#delibRattacheeForm' + delibId).hide();

    $bloc.find('legend span.label').removeClass('label-warning').text('Visualisation');

    $bloc.find('#Multidelib' + delibId + 'Id').prop('disabled', true);
    $bloc.find('#Multidelib' + delibId + 'Deliberation').prop('disabled', true);
    $bloc.find('#Multidelib' + delibId + 'ObjetDelib').prop('disabled', true).val($('#Multidelib' + delibId + 'libelle').text());

    var $tabAnnexe = $('#tableAnnexesdelibRattachee' + delibId);
    $tabAnnexe.find('tr').each(function () {
        if ($(this).hasClass('aSupprimer')) {
            annulerSupprimerAnnexe($(this).attr('data-annexeid'));
        }
        if ($(this).hasClass('aModifier')) {
            annulerModifierAnnexe($(this).attr('data-annexeid'));
        }
    });

    $bloc.find('.actions-multidelib').show();
    $bloc.find('#annulerModifierDelibRattachee' + delibId).hide();
}

// Fonction de suppression d'une délibération rattachée
function supprimerDelibRattachee(delibId) {
    var $bloc = $('#delibRattachee' + delibId);
    $bloc.addClass('aSupprimer');
    $bloc.find('legend span.label').addClass('label-important').text('Supprimer');
    $bloc.find('#MultidelibASupprimer' + delibId).prop('disabled', false);
    $bloc.find('#delibRattacheeDisplay' + delibId).hide();
    $bloc.find('.actions-multidelib').hide();
    $bloc.find('#annulerSupprimerDelibRattachee' + delibId).show();
}

// Fonction de d'annulation de suppression d'une annexe
function annulerSupprimerDelibRattachee(delibId) {
    var $bloc = $('#delibRattachee' + delibId);
    $bloc.removeClass('aSupprimer');
    $bloc.find('legend span.label').removeClass('label-important').text('Visualisation');
    $bloc.find('#MultidelibASupprimer' + delibId).prop('disabled', true);
    $bloc.find('#delibRattacheeDisplay' + delibId).show();
    $bloc.find('#annulerSupprimerDelibRattachee' + delibId).hide();
    $bloc.find('.actions-multidelib').show();
}

// Fonction de suppression du texte de délibération sous forme de fichier joint
function supprimerTextDelibDelibRattachee(delibId) {
    $('#MultidelibDeliberationDisplay' + delibId).hide();
    $('#MultidelibDeliberationAdd' + delibId)
        .html('<input type="file" id="Multidelib' + delibId + 'Deliberation" value="" title="" size="60" name="data[Multidelib][' + delibId + '][deliberation]"></input>')
        .show();
}