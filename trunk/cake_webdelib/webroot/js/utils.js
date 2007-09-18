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
	var br = document.createElement('br');
	p.id = n;
 	d.appendChild(p);

	var div1 = document.createElement('div');
	var input1 = document.createElement('input');
	input1.id = 'AnnexeTitre_'+n;
 	input1.type = 'text';
	input1.size = '30';
	input1.name = 'titre_'+n;
	var titre = document.createTextNode('Titre annexe');	
	div1.appendChild(titre);
	div1.appendChild(input1);
	p.appendChild(div1);

	var div2 = document.createElement('div');
	var input2 = document.createElement('input');
	input2.id = 'AnnexeFile_'+n;
	input2.type = 'file';
	input2.size = '40';
	input2.name = 'file_'+n;
	var chemin = document.createTextNode('Chemin annexe');	
	div2.appendChild(chemin);
	div2.appendChild(br);
	div2.appendChild(input2);
	p.appendChild(div2);
  
	var link = document.createElement('a');
	link.id = 'Lien_'+n;
	link.href = 'javascript:del_field('+n+')';
	text = document.createTextNode('Supprimer');	
	link.appendChild(text);
	p.appendChild(br);
	p.appendChild(link);

	document.getElementById('cible').style.visibility = 'visible';

}
	
function del_field(node)
{
	var node = document.getElementById(node);
	var parent = node.parentNode;
	parent.removeChild(node);


//changement du texte du lien lorsqu'il ne reste plus d'annexe

	var d = document.getElementById('cible');
	var n = d.childNodes.length;

	if (n<2) {
		var a = document.getElementById('lien_annexe');
		a.firstChild.nodeValue = 'Joindre une annexe';
	}
}
	
