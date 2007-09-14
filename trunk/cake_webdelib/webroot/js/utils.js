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


function add_field() {

	var a = document.getElementById('lien_annexe');
	a.firstChild.nodeValue = 'Joindre une autre annexe';

 	var d = document.getElementById('cible');
	var n = d.childNodes.length;
	var p = document.createElement("p");
	p.id = n;
 	d.appendChild(p);

	
	var input1 = document.createElement('input');
	input1.id = 'AnnexeTitre_'+n;
 	input1.type = 'text';
	input1.size = '30';
	input1.name = 'titre_'+n;
	p.appendChild(input1);

	var input2 = document.createElement('input');
	input2.id = 'AnnexeFile_'+n;
	input2.type = 'file';
	input2.size = '40';
	input2.name = 'file_'+n;
	p.appendChild(input2);
	var br = document.createElement('br');
	p.appendChild(br);
  
	var link = document.createElement('a');
	link.id = 'Lien_'+n;
	link.href = 'javascript:del_field('+n+')';
	text = document.createTextNode('Supprimer');	
	link.appendChild(text);
	p.appendChild(link);

	document.getElementById('cible').style.visibility = 'visible';

}
	
function del_field(node)
{
	var node = document.getElementById(node);
	var parent = node.parentNode;
	parent.removeChild(node);
}
	