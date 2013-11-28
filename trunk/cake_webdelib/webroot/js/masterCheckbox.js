/**
 * Créé par Florian Ajir <florian.ajir@adullact.org> le 28/11/13.
 */

$(document).ready(function () {

    // masterCheckbox -> Checkbox
    // La master checkbox coche/décoche tout d'un coup
    $("#masterCheckbox").change(function () {
        if ($(this).prop('checked') === true) {
            $("input[type=checkbox]").prop('checked', true);
        } else {
            $("input[type=checkbox]").prop('checked', false);
        }
    });
    //Checkbox -> masterCheckbox
    $('input[type="checkbox"]').change(function () {
        if ($('input[type="checkbox"]').not('#masterCheckbox').filter(':not(:checked)').length === 0)
            $('#masterCheckbox').prop('checked', true);
        else
            $('#masterCheckbox').prop('checked', false);
    });

});

function selectAll() {
    $('#masterCheckbox').trigger('click');
    return false;
}
