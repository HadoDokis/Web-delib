$(document).ready(function() {
	multiDelib($("#DeliberationIsMultidelib"));
});

function multiDelib(obj) {
	if( $(obj).attr('checked')=='checked') {
		$('#lienTab5').show();
		$('#htextedelib').hide();
                $('#lienTab3').hide();
                var domTextDelib = $('#texteDeliberation').detach();
		$('#texteDelibOngletDelib').append(domTextDelib);
	} else {
                $('#lienTab3').show();
		$('#lienTab5').hide();
                $('#htextedelib').show();
		var domTextDelib = $('#texteDeliberation').detach();
		$('#texteDelibOngletTextes').append(domTextDelib);
	}
}
