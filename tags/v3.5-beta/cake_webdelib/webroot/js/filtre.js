/******************************************************************************/
/* Masque/affiche les crit�res du filtre                                      */
/******************************************************************************/
function basculeCriteres() {
	$cheminIcone = $('#boutonBasculeCriteres').attr('src');
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
	$('#boutonBasculeCriteres').attr('src', $racine+$icone);
}

/******************************************************************************/
/* Modifie l'image de l'icone du filtre si un crit�re du filtre change        */
/******************************************************************************/
function critereChange() {
	$cheminIcone = $('#filtreButton').attr('src');
	$icone = $cheminIcone.substring($cheminIcone.lastIndexOf('/')+1);
	if ($icone == 'filtre.png') {
		$racine = $cheminIcone.substring(0, $cheminIcone.lastIndexOf('/')+1);
		$icone = 'filtreUpdate.png'
		$('#filtreButton').attr('title', 'Cliquer ici pour appliquer les crit�res du filtre');
		$('#filtreButton').attr('src', $racine+$icone);
		$('#filtreButton').attr('onClick', "$('#filtreForm').submit();");
		$('#filtreButton').attr('onMouseOver', "this.style.cursor='pointer'");
	}
}

/******************************************************************************/
/* Annulation du filtre                                                       */
/******************************************************************************/
function razFiltre() {
	// initialise la valeur des crit�res � 0
	$('#filtreCriteres select').val('');

	$('#filtreForm').submit();
}
