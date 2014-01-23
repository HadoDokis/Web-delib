/**
 * Créé par Florian Ajir <florian.ajir@adullact.org> le 28/11/13.
 */

$(document).ready(function () {
    // masterCheckbox -> Checkbox
    // La master checkbox coche/décoche tout d'un coup
    $("#masterCheckbox").change(function () {
        $("input[type=checkbox]").not(":disabled").prop('checked', $(this).prop('checked'));
    });
    //Checkbox -> masterCheckbox
    $('input[type="checkbox"]').change(function () {
        var checked = ($('input[type="checkbox"]').not('#masterCheckbox').filter(':not(:checked)').length === 0);
        $('#masterCheckbox').prop('checked', checked);
    });
    // Si toutes les checkboxes (sauf masterCheckbox) sont cochées, cocher masterCheckbox
    $("#masterCheckbox").prop('checked', $("input[type=checkbox]").not("#masterCheckbox").prop('checked'));
    $("#masterCheckbox").prop('disabled', !$("input[type=checkbox]").not(':disabled').not("#masterCheckbox").length);
});

function selectAll() {
    $('#masterCheckbox').trigger('click');
    return false;
}
