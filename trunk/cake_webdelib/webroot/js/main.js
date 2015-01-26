/**
 * Créé par Florian Ajir <florian.ajir@adullact.org> le 05/03/14.
 *
 * Instructions javascript à éxectuter au lancement de toutes les pages
 */

/**
 * A partir de la date passé on va renvoyer une nouvelle date 
 * en fonction de la combobox et de l'écart passé. L'utilisation 
 * d'une variable date permet de gérer les fin de mois,jour,...
 * 
 * @param {type} dateS date de départ au format yyyy-mm-dd hh:mm:ss
 * @param {type} nb valeur à rajouter 1 => +, -1 => -
 * @returns {String} retourne la date correctement formaté avec l'écarrt voulue
 */
function modifierDate(nom, dateSplit, nb) {
    var separators = ['-', ' ', ':'];
    //on récupère tout les champs un a un
    var tab = dateSplit.split(new RegExp(separators.join('|'), 'g'));
    //on construit la date
    var now = new Date(tab[0], tab[1] - 1, tab[2], tab[3], 0, 0, 0);
    //on ajoute la valeur voulue en fonction de la combox
    if ($(nom).val() == 0) {
        now.setHours(now.getHours() + nb);
    } else if ($(nom).val() == 1) {
        now.setDate(now.getDate() + nb);
    } else if ($(nom).val() == 2) {
        now.setMonth(now.getMonth() + nb);
    } else if ($(nom).val() == 3) {
        now.setFullYear(now.getFullYear() + nb);
    }

    return now.getFullYear() + '-' + ajoutZero((now.getMonth() + 1).toString()) + '-' + ajoutZero(now.getDate().toString()) + ' ' + ajoutZero(now.getHours().toString()) + ':' + ajoutZero(now.getMinutes().toString()) + ':' + ajoutZero(now.getSeconds().toString());
}
/**
 * Ajoute un zero en début de chaine si la taille de data est egale à 1
 * 
 * @param {type} data
 * @returns {String}
 */
function ajoutZero(data) {
    if (data.length == 1) {
        return '0' + data;
    }
    return data;
}

$(document).ready(function () {

    /**
     * Composant pour le retour en haut de page
     */
    $.scrollUp({
        scrollTitle: 'Remonter en haut de la page',
        scrollImg: true,
        scrollDistance: 300, // Distance from top/bottom before showing element (px)
        scrollSpeed: 500, // Speed back to top (ms)
        animation: 'fade', // Fade, slide, none
        animationInSpeed: 500, // Animation in speed (ms)
        animationOutSpeed: 500 // Animation out speed (ms)
    });

    /**
     * Placeholder dans la top-bar
     */
    $('#searchInput').placeholder();

    /**
     * Animation rotation apres clic sur bouton avec icone de roue crantée (running)
     */
    $('a .fa-cog').click(function () {
        $(this).addClass('fa-spin');
    });

    /**
     * Surveillance des champs required (mais non visible, ex: autre onglet) pour afficher un message lors de l'envoi
     */
    $('#boutonValider').click(function () {
        var empty_flds = 0;
        $('input,textarea,select').filter('[required]:not(:visible):not(:disabled)').each(function () {
            if ($.trim($(this).val()) == '') {
                empty_flds++;
            }
        });
        if (empty_flds > 0) {
            if (empty_flds == 1)
                $.jGrowl('<strong>Action impossible :</strong><br>Un champ obligatoire est vide.');
            else
                $.jGrowl('<strong>Action impossible :</strong><br>' + empty_flds + ' champs obligatoires n\'ont pas été saisis');
            return false;
        }
    });
    /**
     * Autocomplete
     */
    // Autres (select multiple)
    $(".select2multiple").select2({
        width: 'resolve',
        allowClear: true,
        width: "100%",
                placeholder: 'Sélection vide'
    });

    $(".selectmultiple").select2({
        width: "100%",
        allowClear: true,
        formatSelection: function (object) {
            // trim sur la sélection (affichage en arbre)
            return $.trim(object.text);
        }
    });

    $(".selectone, #filtreCriteres select").select2({
        //width: "element",
        allowClear: true,
        formatSelection: function (object) {
            // trim sur la sélection (affichage en arbre)
            return $.trim(object.text);
        }
    });


});