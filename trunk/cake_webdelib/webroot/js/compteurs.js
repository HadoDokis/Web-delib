/******************************************************************************/
/* Insère la valeur de l'élément sélectionné du champ select 'select_element' */
/* dans le champ input 'input_name' du formulaire 'form_name'                 */
/* a l'endroit du curseur ou remplace le texte sélectionné                   */
/*                                                                            */
/* @param select_element Objet champ select                                   */
/* @param form_name Nom du formulaire contenant le champ input concerné      */
/* @param input_name Nom du champ input                                       */
/* @access public                                                             */
/******************************************************************************/
function InsertSelectedValueInToInput(e, input_name){
	var input_element =  document.getElementById (input_name);
	input_element.focus();

	/* pour Internet Explorer */
	if(typeof document.selection != 'undefined') {
		/* Insertion du code de formatage */
		var range = document.selection.createRange();
		var insText = range.text;
		range.text = e.value;
		/* Ajustement de la position du curseur */
		range.select();
	}
	/* pour navigateurs basés sur Gecko*/
	else if(typeof input_element.selectionStart != 'undefined')
	{
		/* Insertion du code de formatage */
		var start = input_element.selectionStart;
		var end = input_element.selectionEnd;
		input_element.value = input_element.value.substr(0, start) + e.value + input_element.value.substr(end);
		/* Ajustement de la position du curseur */
		var pos = start + e.value.length;
		input_element.selectionStart = pos;
		input_element.selectionEnd = pos;
	}
	/* pour les autres navigateurs */
	else
	{
		alert("Fonction non implémentée pour votre navigateur");
	};

	e.selectedIndex = 0;
};
