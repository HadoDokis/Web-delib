/**
 * Créé par Florian Ajir <florian.ajir@adullact.org> le 05/03/14.
 *
 * Instructions javascript à éxectuter au lancement de toutes les pages
 */
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
        $(this).addClass('fa-spin')
    });

});