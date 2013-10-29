/**
 * attendable v.2.0
 * Requires: jQuery v1.3+
 * Copyright (c) 2012 Florent Veyrès
 * Modifié par Florian Ajir pour Webdelib
 */

(function ($) {

    $.fn.attendable = function (options) {

        //Fusion des options avec celles par défaut
        $.fn.attendable.options = $.extend({}, $.fn.attendable.defaults, options);

        //Création de l'icone si non présent sur la page
        if ($.fn.attendable.options.loaderImgSrc && $("#attendablePreLoaderImg").length === 0)
            $(document.body).append($('<img>').attr('id', 'attendablePreLoaderImg').attr('src', $.fn.attendable.options.loaderImgSrc).hide());



        return this.each(function () {
            var patt = new RegExp(/confirm\((.+)\)/);
            //Si onclick présent sur l'élément
            if ( patt.test($(this).attr('onclick').toLowerCase())){
                var onclick = $(this).attr('onclick');
                var mesg = patt.exec(onclick);
                mesg = (mesg[1]).replace(/'/g, "");
                $(this).removeAttr('onclick');
                $(this).click(function () {
                    if (confirm(mesg)){
                        $(this).attendableAffiche();
                        pauseWhileDownload(this);
                    }
                    else return false;
                });
            }else{
                $(this).click(function () {
                    $(this).attendableAffiche();
                    pauseWhileDownload(this);
                });
            }
        });
    };

    $.fn.attendable.options = {};

    $.fn.attendable.defaults = {
        toOverlayDomId: 'container',
        overlayDomId: 'overlay',
        modalDomId: 'modalAttendable',
        message: 'veuillez patienter',
        loaderImgSrc: '/img/ajax-loader.gif'
    };

    $.fn.attendableAffiche = function (confirmTxt) {
        if ((typeof confirmTxt !== "undefined") && confirmTxt !== '' && !confirm(confirmTxt))
            return false;
        var domModal = $('<div>').attr('id', $.fn.attendable.options.modalDomId);
        //Fix si modale a été supprimer, recharger l'image du loader
        if ($.fn.attendable.options.loaderImgSrc && $("#attendablePreLoaderImg").length === 0)
            $(document.body).append($('<img>').attr('id', 'attendablePreLoaderImg').attr('src', $.fn.attendable.options.loaderImgSrc).hide());

        if ($.fn.attendable.options.loaderImgSrc)
            domModal.append($('#attendablePreLoaderImg').css('display', '').detach()).append('<br><br>');
        domModal.append($.fn.attendable.options.message);
        $(document.body).append(domModal);
        $(document.body).append($('<div>').attr('id', $.fn.attendable.options.overlayDomId));
        $.fn.attendableResize();

        return true;
    };

    $.fn.attendableResize = function () {
        // initialisation
        var domOverlay = $('#' + $.fn.attendable.options.overlayDomId);
        if (domOverlay.length === 0)
            return;

        // position du div modal
        var domModal = $('#' + $.fn.attendable.options.modalDomId);
        var modalTop = "50%";
        var modalLeft = "50%";
        domModal.css('left', modalLeft).css('top', modalTop);
    };

    $(window).resize(function () {
        $.fn.attendableResize();
    });
})(jQuery);