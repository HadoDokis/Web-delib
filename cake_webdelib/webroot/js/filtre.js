/**
 * appelée à l'ouverture
 */
$(document).ready(function() {
	$('#filtreCriteres .submit').hide();
	if ($('#filtreFoncAffiche').val() == 0)
		$('#filtreCriteres').hide();
});

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
	/*$cheminIcone = $('#boutonBasculeCriteres').attr('src');
	$racine = $cheminIcone.substring(0, $cheminIcone.lastIndexOf('/')+1);
	$icone = $cheminIcone.substring($cheminIcone.lastIndexOf('/')+1);
	if ($icone == 'filtreUp.png') {
		$icone = 'filtreDown.png'
		$('#filtreCriteres').slideDown();
		$('#filtreFoncAffiche').val(1);
	} else {
		$icone = 'filtreUp.png'
		$('#filtreCriteres').slideUp();
		$('#filtreFoncAffiche').val(0);
	}
	$('#boutonBasculeCriteres').attr('src', $racine+$icone);*/
}

/**
 * Modifie l'image de l'icone du filtre si un critère du filtre change
 */
function critereChange(element) {
	$cheminIcone = $('#filtreButton').attr('src');
	$icone = $cheminIcone.substring($cheminIcone.lastIndexOf('/')+1);
	if ($icone == 'filtre.png') {
		$racine = $cheminIcone.substring(0, $cheminIcone.lastIndexOf('/')+1);
		$icone = 'filtreUpdate.png'
		$('#filtreButton').attr('title', 'Cliquer ici pour appliquer les critères du filtre');
		$('#filtreButton').attr('src', $racine+$icone);
		$('#filtreButton').attr('onClick', "$('#filtreForm').submit();");
        $('#filtreButton').attr('class', "applyFilter");
	}
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
    $('#filtreCriteres select').val('');
    $('#filtreCriteres input').val('');
    $('#filtreForm').submit();
}
