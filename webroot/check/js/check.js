(function($) {
    $(document).ready(function() {
	    $('[rel="tooltip"]').tooltip();
	    $('[rel="popover"]').popover({trigger: 'hover'});


	    $('.navbar .dropdown-toggle').hover(function( ) {
				$('.dropdown').removeClass('open');
				$(this).parent('.dropdown').addClass('open');
		}, function(e) {
		});

	    $('.dropdown-menu').mouseleave(function() {
		    $(this).parent('.dropdown').removeClass('open');
		});

	/* domain registration random domain suggestion */
	$('.domainregistration').each(function() {
	    var domains = ['petit-poney.fr', 'mon-poney.net', 'i-love-rpn.com', 'dedibox-wopr.fr'];
	    var val = $('input', this).attr('placeholder');
	    val = val.split(':')[0] + ': ' + domains[Math.floor(Math.random() * domains.length)];
	    $('input', this).attr('placeholder', val);
	});

	/* backtotop smooth slider */
	$('.backtotop').click(function(e) {
	    $('html,body')
		.animate({scrollTop: 0}, 600);
	    e.preventDefault();
	    return false;
	});

	$('a.slideInPage').click(function(e) {
	    var dest = $(this).attr('href');
	    console.log(dest);
	    var destElem = $(dest);
	    var yPosition = destElem.offset().top - 150;
	    var baseColor = destElem.css('background-color');
	    $('html,body').animate({scrollTop: yPosition}, 300, function() {
		destElem.animate({"background-color": '#ffeecc'}, 300, function() {
		    destElem.animate({"background-color": baseColor}, 500);
		});
	    });
	    e.preventDefault();
	});

	/* hack multi-column responsive */
	var switchMode = 3;
	var switchLayout = function() {
	    var winWidth = $(window).width();
	    if (switchMode != 2 && winWidth < 980 && winWidth >= 768) {
		$('.middle-area').removeClass('span6').addClass('span9');
		$('.middle-area').prepend($('.middle-area-inner'));
		$('.left-area').prepend($('.right-area-inner'));
		switchMode = 2;
	    } else if (switchMode != 3 && winWidth >= 980) {
		$('.middle-area').removeClass('span9').addClass('span6');
		$('.middle-area').prepend($('.middle-area-inner'));
		$('.right-area').prepend($('.right-area-inner'));
		switchMode = 3;
	    } else if (switchMode != 1 && winWidth < 767) {
		$('.left-area').prepend($('.middle-area-inner'));
		switchMode = 1;
	    }
	};
	if ($('.middle-area').hasClass('span6') &&
	    $('.right-area').hasClass('span3') &&
	    $('.left-area').hasClass('span3')) {
	    switchLayout();
	    $(window).resize(switchLayout);
	}

	$('.carousel').carousel({
	    interval: 5000,
	    pause: 'hover'
	});

	try {
	    $('a.colorbox').colorbox();
	} catch (e) {

	}

	});
})(jQuery);
