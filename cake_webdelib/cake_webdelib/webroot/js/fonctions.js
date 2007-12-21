function lister_services(params, url)
{
	var urlb=url+params.value;
	document.location=urlb;
}

function lister_circuits(params, url)
{
	var urlb=url+params.value;
	document.location=urlb;
}


function checkSelectedCircuit($id)
{
	if($id=="0")
	{
		alert('Vous devez d\'abord choisir un circuit');
	}
}
