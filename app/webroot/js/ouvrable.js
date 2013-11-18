/**
 * ouvrable v.1.0
 * Requires: jQuery v1.3+
 * Copyright (c) 2011 Florent Veyrès
 */
(function($) {
$.fn.ouvrable = function(options) {
	var o = $.extend({}, $.fn.ouvrable.defaults, options);
	var arrow = '';

	return this.each(function(index) {
		// titre
		var domTitle = $(this).find(o.titleTag).first();
		if (domTitle.length==1)
			domTitle.detach();
		else
			var domTitle = $('<'+o.titleTag+'/>').append(o.title);
		// corps
		$(this).wrapInner(function(index) {
			var corps = $('<div/>').addClass('ouvrable-corps');
			// taille initiale
			if (o.initHeight == 'MIN') {
				(o.minHeight == '') ? corps.hide() : corps.css('max-height', o.minHeight);
				arrow = o.arrowUp;
			} else {
				if (o.maxHeight != '') corps.css('max-height', o.maxHeight);
				arrow = o.arrowDown;
			}
			return corps;
		});
		// entête
		var ajouteFleche = true;
		if (arrow == o.arrowDown) {
			var minHeight = (o.minHeight == '') ? 0 : parseInt(o.minHeight);
			var ouvrableDivHeight = $(this).find('.ouvrable-corps').height();
			ajouteFleche = minHeight<ouvrableDivHeight;
		}
		if (ajouteFleche) {
			var domFleche = $('<img/>').attr('src', arrow).attr('alt', '');
			domTitle.prepend(domFleche)
				.attr('onMouseOver', "this.style.cursor='pointer'")
				.click(function(){onClick(domFleche);});
		}
		var domEntete = $('<div/>').addClass('ouvrable-entete').append(domTitle);
		$(this).prepend(domEntete);
	});

	function onClick(obj) {
		var $this = $(obj);
		var corpsDiv = $this.parent().parent().next();
		if ($this.attr('src') == o.arrowUp) {
			$this.attr('src', o.arrowDown);
			if (o.maxHeight == '')
				corpsDiv.hide().css('max-height', '').slideDown('slow');
			else {
				if (o.minHeight == '')
					corpsDiv.css('max-height', o.maxHeight).slideDown('slow');
				else
					corpsDiv.animate({maxHeight: o.maxHeight}, 'slow');
			}
		} else {
			$this.attr('src', o.arrowUp);
			if (o.minHeight == '')
				corpsDiv.slideUp('slow', function(index){$(this).css('max-height', '')});
			else
				corpsDiv.animate({maxHeight: o.minHeight}, 'slow');
		}
	};

};

$.fn.ouvrable.defaults = {
	title : 'Zone ouvrable',
	titleTag : '*',
	initHeight : 'MAX', // état d'ouverture à l'affichage initial : 'MIN'||'MAX'
	minHeight : '', // ouverture minimum : ''||'npx'
	maxHeight : '', // ouverture maximum : ''||'npx'
	arrowUp : 'img/arrow-up.gif',
	arrowDown : 'img/arrow-down.gif'
};

})(jQuery);
