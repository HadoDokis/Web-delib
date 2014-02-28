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
            // Titre de la fenêtre modale
            var titre = null;
            if ($(this).attr('data-modal') !== undefined) titre = $(this).attr('data-modal');
            else if ($(this).hasClass('delib_pdf') || $(this).hasClass('link_pdf')) titre = 'Génération du document';

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
                            $(this).attendableAffiche(titre);
                            pauseWhileDownload($(this));
                        }
                        else return false;
                    });
                } else {
                    $(this).click(function () {
                        $(this).attendableAffiche(titre);
                        pauseWhileDownload($(this));
                    });
                }
            } else if (this.tagName == 'FORM') {
                $(this).on('submit', function () {
                    $(this).attendableAffiche(titre);
                });
            }
        });
    };

    $.fn.attendable.options = {};

    $.fn.attendable.defaults = {
        toOverlayDomId: 'container',
        overlayDomId: 'overlay',
        modalDomId: 'modalAttendable',
        message: 'Veuillez patienter...',
        loaderImgSrc: '/img/ajax-loader.gif'
    };

    $.fn.attendableAffiche = function (titre) {
        if (titre == null) titre = 'Action en cours de traitement';
        var domModal = $('<div>').attr('id', $.fn.attendable.options.modalDomId);
        domModal.attr('class', 'modal hide fade');
        domModal.attr('tabindex', '-1');
        domModal.attr('role', 'dialog');
        domModal.css({width:'auto', height:'auto'});
        
        var divTitle=$('<h3>').append(titre);
        domModal.append($('<div>').attr('class', 'modal-header').append(divTitle));
        
        var divBody=$('<div>').attr('class', 'modal-body');
        //Fix si modale a été supprimer, recharger l'image du loader
//        if ($.fn.attendable.options.loaderImgSrc && $("#attendablePreLoaderImg").length === 0)
         divBody.append($('<img>').attr('id', 'attendablePreLoaderImg').attr('src', $.fn.attendable.options.loaderImgSrc)).append('<br><br>');
//        if ($.fn.attendable.options.loaderImgSrc)
//        
          //divBody.append($('#attendablePreLoaderImg').css('display', '').detach()).append('<br><br>');
        
        divBody.append($.fn.attendable.options.message);
        divBody.append($('<div>').attr('id', $.fn.attendable.options.overlayDomId));
        domModal.append(divBody);
        
        var divFooter=$('<div>').attr('class', 'modal-footer');
        domModal.append(divFooter);
        
        $(document.body).append(domModal);
        
        $('#'+$.fn.attendable.options.modalDomId).modal({backdrop:'static',
                                                      //   remote:'remote'
                                                     });
        //$.fn.attendableResize();

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