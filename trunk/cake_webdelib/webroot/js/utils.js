window.onload=montre;
function montre(id) {
	var d = document.getElementById(id);
		for (var i = 1; i<=15; i++) {
			if (document.getElementById('smenu'+i)) {document.getElementById('smenu'+i).style.display='none';}
		}
	if (d) {d.style.display='block';}
}



function changeService(params)
{
  	var url = params.id+"services/changeService/"+params.value;
	document.location=url;
}