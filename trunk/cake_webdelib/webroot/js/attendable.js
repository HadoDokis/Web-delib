/**
 * attendable
 * Requires: jQuery v1.3+, Bootstrap
 */

$(document).ready(function () {
    $(".waiter").attendable({});
    $(".delib_pdf").attendable({titre: 'Génération du document'});
    $("a.link_clore_seance").attendable({titre: 'Cloture de la séance'});
    $('form#DeliberationSendToParapheurForm, form#DeliberationAutreActesValidesForm').attendable({titre: 'Envoi du dossier au Parapheur'});
});


$.fn.attendable = function (options) {

    var defaults = {
            titre: 'Action en cours de traitement'
        },
        modalOptions = {
            backdrop: 'static',
            keyboard: false
        },
        titre,
        options = $.extend({}, defaults, options);//Fusion des options avec celles par défaut

    return this.each(function () {
        // Titre de la fenêtre modale
        if ($(this).attr('data-modal') !== undefined)
            titre = $(this).attr('data-modal');
        else
            titre = options.titre;

        if (this.tagName == 'A') {
            var patt = new RegExp(/confirm\((.+)\)/);
            //Si onclick présent sur l'élément
            if ($(this).attr('onclick') && patt.test($(this).attr('onclick').toLowerCase())) {
                var onclick = $(this).attr('onclick');
                var mesg = patt.exec(onclick);
                mesg = (mesg[1]).replace(/'/g, "");
                $(this).removeAttr('onclick');
                $(this).click(function () {
                    if (confirm(mesg)) {
                        $('#waiter-title').text(titre);
                        $("#waiter").modal(modalOptions);
                        pauseWhileDownload($(this));
                    }
                    else return false;
                });
            } else {
                $(this).click(function () {
                    $('#waiter-title').text(titre);
                    $("#waiter").modal(modalOptions);
                    pauseWhileDownload($(this));
                });
            }
        } else if (this.tagName == 'FORM') {
            $(this).on('submit', function () {
                $('#waiter-title').text(titre);
                $("#waiter").modal(modalOptions);
            });
        }
    });
};

function pauseWhileDownload(elt) {
    var token = setToken();
    //Ajout du num de cookie en dernier argument url
    var href = $(elt).attr('href');
    if (href) {
        if (href.substr(href.length - 1) != '/')
            href += '/';
        $(elt).attr('href', href + token);
    }
    blockUI(elt, token); //Veuillez patienter pendant la génération du document
}

/**
 * génère un numéro unique (token) à partir du temps courant
 * @returns {number} Token
 */
function setToken() {
    var downloadToken = new Date().getTime();
    return downloadToken.toString();
}

// Prevents double-submits by waiting for a cookie from the server.
function blockUI(elt, token) {
    var downloadTimer = window.setInterval(function () {
        $.ajax({
            url: '/models/genereToken/' + token,
            cache: false,
            type: 'GET',
            dataType: 'json',
            async: false,
            success: function (response) {
                if (token.toString() === response.downloadToken) {
                    window.clearInterval(downloadTimer);
                    unblockUI(elt, token);
                }
            }
        });
    }, 500);
}

function unblockUI(elt, token) {
    //Suppression du token de la fin d'url
    if ($(elt).attr('href')) {
        var href = $(elt).attr('href');
        $(elt).attr('href', href.replace('/' + token, ''));
    }
    $("#waiter").modal('hide');
}
