window.onload=initAffichage;

function initAffichage() {

// Saisie des présents masquée
	blocElement = document.getElementById("saisiePresents");
	blocElement.style.display = 'none';

// Affichage du type de saisie du vote
	selectElement = document.getElementById("VoteTypeVote");
	selectElement.value=1;
	affichageTypeVote(selectElement);
}

function affichageTypeVote(selectElement) {
	blocElement = document.getElementById("voteDetail");
	blocElement.style.display = (selectElement.value == 1) ? '' : 'none';
	blocElement = document.getElementById("voteTotal");
	blocElement.style.display = (selectElement.value == 2) ? '' : 'none';
	blocElement = document.getElementById("voteResultat");
	blocElement.style.display = (selectElement.value == 3) ? '' : 'none';
}
function affichageListePresents(selectElement) {
	blocElement = document.getElementById("saisiePresents");
	blocElement.style.display = (selectElement.value == 2) ? '' : 'none';
}


function vote(){

document.getElementById('VoteRes0').value=0;
document.getElementById('VoteRes1').value=0;
document.getElementById('VoteRes2').value=0;
document.getElementById('VoteRes3').value=0;


var mesBoutons = document.getElementsByTagName('input');

for (i = 0; i < mesBoutons.length; i++) {



if(mesBoutons[i].checked ==true && mesBoutons[i].type =='radio' && mesBoutons[i].name !='global'){

	if(mesBoutons[i].value==2){
		if(document.getElementById('VoteRes0').value=='')
		{document.getElementById('VoteRes0').value=1;}
		else{document.getElementById('VoteRes0').value=eval(document.getElementById('VoteRes0').value)+1;}
	}
	if(mesBoutons[i].value==3){
		if(document.getElementById('VoteRes1').value=='')
		{document.getElementById('VoteRes1').value=1;}
		else{document.getElementById('VoteRes1').value=eval(document.getElementById('VoteRes1').value)+1;}

	}
	if(mesBoutons[i].value==4){
		if(document.getElementById('VoteRes2').value=='')
		{document.getElementById('VoteRes2').value=1;}
		else{document.getElementById('VoteRes2').value=eval(document.getElementById('VoteRes2').value)+1;}

	}
	if(mesBoutons[i].value==5){
		if(document.getElementById('VoteRes3').value=='')
		{document.getElementById('VoteRes3').value=1;}
		else{document.getElementById('VoteRes3').value=eval(document.getElementById('VoteRes3').value)+1;}
	}

}

if(mesBoutons[i].checked==true && mesBoutons[i].name =='global'){
mesBoutons[i].checked=false;
}

}
}



function vote_global(rep){

var mesBoutons = document.getElementsByTagName('input');
document.getElementById('VoteRes0').value=0;
document.getElementById('VoteRes1').value=0;
document.getElementById('VoteRes2').value=0;
document.getElementById('VoteRes3').value=0;

for (i = 0; i < mesBoutons.length; i++) {
if(mesBoutons[i].value ==rep && mesBoutons[i].type =='radio'){
	mesBoutons[i].checked=true;
	if(rep==2){
		if(document.getElementById('VoteRes0').value=='')
		{document.getElementById('VoteRes0').value=1;}
		else{document.getElementById('VoteRes0').value=eval(document.getElementById('VoteRes0').value)+1;}
	}
	if(rep==3){
		if(document.getElementById('VoteRes1').value=='')
		{document.getElementById('VoteRes1').value=1;}
		else{document.getElementById('VoteRes1').value=eval(document.getElementById('VoteRes1').value)+1;}

	}
	if(rep==4){
		if(document.getElementById('VoteRes2').value=='')
		{document.getElementById('VoteRes2').value=1;}
		else{document.getElementById('VoteRes2').value=eval(document.getElementById('VoteRes2').value)+1;}

	}
	if(rep==5){
		if(document.getElementById('VoteRes3').value=='')
		{document.getElementById('VoteRes3').value=1;}
		else{document.getElementById('VoteRes3').value=eval(document.getElementById('VoteRes3').value)+1;}

	}

}

}

	if(rep==2){
		document.getElementById('VoteRes0').value=eval(document.getElementById('VoteRes0').value)-1;}

	if(rep==3){
		document.getElementById('VoteRes1').value=eval(document.getElementById('VoteRes1').value)-1;}

	if(rep==4){
		document.getElementById('VoteRes2').value=eval(document.getElementById('VoteRes2').value)-1;}

	if(rep==5){
		document.getElementById('VoteRes3').value=eval(document.getElementById('VoteRes3').value)-1;}


}