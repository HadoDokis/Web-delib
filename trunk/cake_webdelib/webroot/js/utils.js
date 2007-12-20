window.onload=montre;

function montre(id) {
	var d = document.getElementById(id);
		for (var i = 1; i<=15; i++) {
			if (document.getElementById('smenu'+i)) {document.getElementById('smenu'+i).style.display='none';}
		}
	if (d) {d.style.display='block';}
}

function OuvrirFenetre(url,nom,detail) {
	var w = window.open(url,nom,detail);
}
function FermerFenetre(){
var choix = confirm("Voulez-vous fermer la fenetre ?");
if (choix)  window.close();
//else alert("Vous avez cliqué sur ANNULER ou vous avez fermé");
}

function returnChoice(text,arg) {

    var1 = 'classif1';
    var2 = 'classif2';
    elt1 = window.opener.document.getElementById(var1);
    elt2 = window.opener.document.getElementById(var2);

    if (text){
    	elt1.value = text;
    	elt2.value = arg;
    } else {
      elt1.value = '';
      elt2.value = '';
    }
	var a = window.opener.document.getElementById('classification_text');
  	a.firstChild.nodeValue = '[Modifier la classification]';

  window.close();
}

function return_choice_lot(text,arg,delibId) {

    var1 = delibId+'classif1';
    var2 = delibId+'classif2';
    elt1 = window.opener.document.getElementById(var1);
    elt2 = window.opener.document.getElementById(var2);
    if (text){
    	elt1.value = text;
    	elt2.value = arg;
    } else {
      elt1.value = '';
      elt2.value = '';
    }
	var a = window.opener.document.getElementById(delibId+'_classification_text');
  	//a.firstChild.nodeValue = '[Changer la localisation]';
    window.close();
}

function disable(id,val)
{
  if (val=='1')
  	document.getElementById(id).disabled=true;
  else
  document.getElementById(id).disabled=false;
}


function saveLocation(idDelib,idLoc,zone)
{

	if(idLoc.value== ""){
	var url = idLoc.id+"deliberations/saveLocation/"+idDelib+"/0/"+zone;
	document.location=url;

	}else{
	var url = idLoc.id+"deliberations/saveLocation/"+idDelib+"/"+idLoc.value+"/"+zone;
	document.location=url;
	}
}

function changeLocation1(idDelib,zone1,zone2,zone3)
{
	var url = zone1.id+"deliberations/changeLocation/"+idDelib+"/"+zone1.value+"/"+zone2+"/"+zone3;
	document.location=url;
}

function changeLocation2(idDelib,zone1,zone2,zone3)
{
	var url = zone2.id+"deliberations/changeLocation/"+idDelib+"/"+zone1+"/"+zone2.value+"/"+zone3;
	document.location=url;
}

function changeLocation3(idDelib,zone1,zone2,zone3)
{
	var url = zone3.id+"deliberations/changeLocation/"+idDelib+"/"+zone1+"/"+zone2+"/"+zone3.value;
	document.location=url;
}



function changeService(params)
{
	var url = params.id+"services/changeService/"+params.value;
	document.location=url;
}

function changeRapporteur(params,delib)
{
	var url = params.id+"seances/changeRapporteur/"+params.value+"/"+delib;
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
	div1.appendChild(document.createElement('br'));
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
	div2.appendChild(document.createElement('br'));
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

