/**
 * Créé par Florian Ajir <florian.ajir@adullact.org> le 28/11/13.
 *
 * Comportement des checkboxes / master checkbox
 * Select All / Deselect All
 * Initial state
 */
$(document).ready(function () {
    // masterCheckbox -> Checkbox
    // La master checkbox coche/décoche tout d'un coup
    $("#masterCheckbox").change(function () {
        $("input[type=checkbox]").not(':disabled').prop('checked', $(this).prop('checked'));
    });
    //Checkbox -> masterCheckbox
    $('input[type="checkbox"]').not("#masterCheckbox").change(function () {
        $('#masterCheckbox').prop('checked', $('input[type="checkbox"]').not('#masterCheckbox').not(':disabled').not(':checked').length === 0);
    });
    // Si toutes les checkboxes (sauf masterCheckbox) sont cochées, cocher masterCheckbox
    $("#masterCheckbox").prop('checked', $("input[type=checkbox]").not(':disabled').not(':checked').not("#masterCheckbox"));
    $("#masterCheckbox").prop('disabled', !$("input[type=checkbox]").not(':disabled').not("#masterCheckbox").length);

});

function selectAll() {
    $('#masterCheckbox').trigger('click');
    return false;
}
