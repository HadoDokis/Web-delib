/**
 * Masque/affiche les critères du filtre
 */
function basculeCriteres() {
    
    if ($('#filtreCriteres').is( ":hidden" ))
    {
        $('#filtreCriteres').slideDown();
    }
    else {
        $('#filtreCriteres').slideUp();
    }
}

/**
 * Modifie l'image de l'icone du filtre si un critère du filtre change
 */
function critereChange(element) {
    // Clonage du bouton appliquer filtre et insertion à droite du champ modifié
    var selector = ".input";
    if ($(element).closest('div').hasClass("date")){
        selector = ".date";
    }
    if ($(element).closest(selector).find(".applyFilter").length === 0){
        var btn = $('#filtreButton').clone();
        btn.removeAttr('id').addClass('minifiltre');
        $(element).closest(selector).append(btn);
    }
}

/**
 * Annulation du filtre
 */
function razFiltre() {
    // initialise la valeur des critères à 0
    $('#filtreCriteres').slideUp("slow", function() {
        $('#filtreCriteres select').val('');
        $('#filtreCriteres input').val('');
        $('#filtreFonc.affiche').val(0);
        $('#filtreForm').submit();
    });
}
