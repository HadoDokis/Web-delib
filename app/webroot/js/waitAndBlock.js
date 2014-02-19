/**
 * waitAndBlock v.1.0
 * Requires: jQuery v1.3+ & attendable.js
 * Copyright (c) 2013 Florian Ajir
 */
$(document).ready(function() {
    $("a.delib_pdf").attendable();
    $("a.link_clore_seance").attendable();
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
    return downloadToken.toString();
}

// Prevents double-submits by waiting for a cookie from the server.
function blockUI(elt, token) {
    var retourRequete=true;
    var downloadTimer;

    downloadTimer = window.setInterval(function() {
        if(retourRequete){
            retourRequete=false;
            $.ajax({
            url: '/models/generationToken/'+token,
            cache: false,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                try
                {
                    if (token.toString() === response.downloadToken) {
                        window.clearInterval(downloadTimer);
                        unblockUI(elt, token);
                    }else
                        retourRequete=true;
                }
                catch(err)
                {
                    alert(err);
                    retourRequete=true;
                }                
            },
            error: function(error)
            {
               alert(error);
               unblockUI(elt, token);
            }
            });
        }
    }, 2000);
}

function unblockUI(elt, token) {
    //Suppression du token de la fin d'url
    if ($(elt).attr('href')){
        var href = $(elt).attr('href');
        $(elt).attr('href', href.replace(token, ''));
    }
    $("#overlay").remove();
    $("#modalAttendable").remove();
}