$(document).ready(function() {
	multiDelib($("#DeliberationIsMultidelib"));
});

function multiDelib(obj) {
	if( $(obj).attr('checked')=='checked') {
		var domTextDelib = $('#texteDeliberation').detach();
		$('#texteDelibOngletDelib').append(domTextDelib);
		$('#lienTab5').show();
		$('#htextebelib').hide();
	} else {
		$('#lienTab5').hide();
                $('#htextebelib').show();
		var domTextDelib = $('#texteDeliberation').detach();
		$('#texteDelibOngletTextes').append(domTextDelib);
	}
}
