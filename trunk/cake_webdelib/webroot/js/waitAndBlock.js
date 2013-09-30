/**
 * waitAndBlock v.1.0
 * Requires: jQuery v1.3+
 * Copyright (c) 2013 Florian Ajir
 */
$(document).ready(function() {
    $("a.link_pdf").attendable({message: 'Veuillez patienter pendant la génération du pdf'});
});

function pauseWhileDownload(elt) {
    var token = setToken();
    $(elt).attr('href', $(elt).attr('href') + token);
    blockUI(elt, token); //Veuillez patienter pendant la génération du pdf
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
    //Suppression du token
    var href = $(elt).attr('href');
    $(elt).attr('href', href.replace(token, ''));
    $("#overlay").remove();
    $("#modalAttendable").remove();
}