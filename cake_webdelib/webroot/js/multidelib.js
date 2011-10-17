$(document).ready(function() {
multiDelib($("#radio_multi"));
});

function multiDelib(obj) {
	if( $(obj).attr('checked')=='checked') {
		var domTextDelib = $('#texteDeliberation').detach();
		$('#texteDeliberationMulti').append(domTextDelib);
		$('#lienTab5').show();
	} else {
		$('#lienTab5').hide();
		var domTextDelib = $('#texteDeliberationMulti').detach();
		$('#texteDeliberation').append(domTextDelib);
	}
}
