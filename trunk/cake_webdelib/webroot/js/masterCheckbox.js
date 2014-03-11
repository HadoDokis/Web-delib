/**
 * Créé par Florian Ajir <florian.ajir@adullact.org> le 28/11/13.
 */

$(document).ready(function () {
    // masterCheckbox -> Checkbox
    // La master checkbox coche/décoche tout d'un coup
    $("#masterCheckbox").change(function () {
        $("input[type=checkbox]").filter(':not(:disabled)').prop('checked', $(this).prop('checked'));
    });
    //Checkbox -> masterCheckbox
    $('input[type="checkbox"]').not("#masterCheckbox").change(function () {
        var checked = ($('input[type="checkbox"]').not('#masterCheckbox').filter(':not(:disabled)').filter(':not(:checked)').length === 0);
        $('#masterCheckbox').prop('checked', checked);
    });
    // Si toutes les checkboxes (sauf masterCheckbox) sont cochées, cocher masterCheckbox
    $("#masterCheckbox").prop('checked', $("input[type=checkbox]").filter(':not(:disabled)').not("#masterCheckbox").prop('checked'));
    $("#masterCheckbox").prop('disabled', !$("input[type=checkbox]").filter(':not(:disabled)').not("#masterCheckbox").length);

    //Mater Checkbox spécifique à la table des droits
    // La master checkbox coche/décoche tout d'un coup
    $("#masterCheckboxDroits").change(function () {
        $("#tableDroits input[type=checkbox]").filter(':not(:disabled)').prop('checked', $(this).prop('checked'));
    });
    //Checkbox -> masterCheckbox
    $('#tableDroits input[type="checkbox"]').not("#masterCheckboxDroits").change(function () {
        var checked = ($('#tableDroits input[type="checkbox"]').not('#masterCheckboxDroits').filter(':not(:disabled)').filter(':not(:checked)').length === 0);
        $('#masterCheckboxDroits').prop('checked', checked);
    });
    // Si toutes les checkboxes (sauf masterCheckbox) sont cochées, cocher masterCheckbox
    $("#masterCheckboxDroits").prop('checked', $("#tableDroits input[type=checkbox]").filter(':not(:disabled)').not("#masterCheckboxDroits").prop('checked'));
    $("#masterCheckboxDroits").prop('disabled', !$("#tableDroits input[type=checkbox]").filter(':not(:disabled)').not("#masterCheckboxDroits").length);



});

function selectAll() {
    $('#masterCheckbox').trigger('click');
    return false;
}
