$(document).ready(function () {
    $("#DeliberationIsMultidelib").change(function() {
    var $input = $( this );
    if( $input.prop( "checked")) {
            $('#lienTab5').show();
            $('#htextedelib').hide();
            $('#lienTab3').hide();
            $('#delibPrincipaleAnnexeRatt').append($('#DelibPrincipaleAnnexes').detach());
            $('#texteDelibOngletDelib').append($('#texteDeliberation').detach());
        }
        else{
            $('#lienTab3').show();
            $('#lienTab5').hide();
            $('#htextedelib').show();
            $('#DelibOngleAnnexes').append($('#DelibPrincipaleAnnexes').detach());
            $('#texteDelibOngletTextes').append($('#texteDeliberation').detach());

        }
    }).change();
});