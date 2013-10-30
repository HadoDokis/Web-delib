/**
 * waitAndBlock v.1.0
 * Requires: jQuery v1.3+
 * Copyright (c) 2013 Florian Ajir
 */
$(document).ready(function() {
    $("a.delib_pdf").attendable({message: 'Veuillez patienter pendant la génération du document'});
    $("a.link_clore_seance").attendable({message: 'Veuillez patienter pendant la clotûre de la séance'});
});

function pauseWhileDownload(elt) {
    var token = setToken();
    //Ajout du num de cookie en dernier argument url
    if ($(elt).attr('href')){
        $(elt).attr('href', $(elt).attr('href')+ "/" + token);
    }
    blockUI(elt, token); //Veuillez patienter pendant la génération du document
}

function getCookie(name) {
    var parts = document.cookie.split(name + "=");
    if (parts.length === 2)
        return parts.pop().split(";").shift();
}

function expireCookie(cName) {
    document.cookie =
            encodeURIComponent(cName) +
            "=deleted; expires=" +
            new Date(0).toUTCString();
}

/**
 * génère un numéro unique (token) à partir du temps courant
 * @returns {number} Token
 */
function setToken() {
    var downloadToken = new Date().getTime();
    return downloadToken;
}

var downloadTimer;
var attempts = 120;

// Prevents double-submits by waiting for a cookie from the server.
function blockUI(elt, downloadToken) {
    downloadTimer = window.setInterval(function() {
        var token = getCookie("downloadToken");
        if ((token == downloadToken) || (attempts === 0)) {
            unblockUI(elt, downloadToken);
        }
        attempts--;
    }, 1000);
}

function unblockUI(elt, token) {
    window.clearInterval(downloadTimer);
    expireCookie("downloadToken");
    //Suppression du token de la fin d'url
    if ($(elt).attr('href')){
        var href = $(elt).attr('href');
        $(elt).attr('href', href.replace(token, ''));
    }
    $("#overlay").remove();
    $("#modalAttendable").remove();
}