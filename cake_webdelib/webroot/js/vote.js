function vote(name){
var max;
var valmax;
var name = name;

document.getElementsByName('global')[0].disabled=true;
document.getElementsByName('global')[1].disabled=true;
document.getElementsByName('global')[2].disabled=true;
document.getElementsByName('global')[3].disabled=true;

// document.getElementById('res1_hidden').value=0;
// document.getElementById('res0_hidden').value=0;

if(document.getElementsByName(name)[0].checked==true)
	{document.getElementsByName(name)[0].disabled=true;
document.getElementsByName(name)[1].disabled=true;
document.getElementsByName(name)[2].disabled=true;
document.getElementsByName(name)[3].disabled=true;
	if(document.getElementById('DeliberationRes1').value=='')
		{document.getElementById('DeliberationRes1').value=1;}
		else{document.getElementById('DeliberationRes1').value=eval(document.getElementById('DeliberationRes1').value)+1;}}
else if(document.getElementsByName(name)[1].checked==true)
	{document.getElementsByName(name)[0].disabled=true;
document.getElementsByName(name)[1].disabled=true;
document.getElementsByName(name)[2].disabled=true;
document.getElementsByName(name)[3].disabled=true;
	if(document.getElementById('DeliberationRes0').value=='')
		{document.getElementById('DeliberationRes0').value=1;}
		else{document.getElementById('DeliberationRes0').value=eval(document.getElementById('DeliberationRes0').value)+1;}}
else if(document.getElementsByName(name)[2].checked==true)
	{document.getElementsByName(name)[0].disabled=true;
document.getElementsByName(name)[1].disabled=true;
document.getElementsByName(name)[2].disabled=true;
document.getElementsByName(name)[3].disabled=true;
	if(document.getElementById('DeliberationRes2').value=='')
		{document.getElementById('DeliberationRes2').value=1;}
		else{document.getElementById('DeliberationRes2').value=eval(document.getElementById('DeliberationRes2').value)+1;}}
else if(document.getElementsByName(name)[3].checked==true)
	{document.getElementsByName(name)[0].disabled=true;
document.getElementsByName(name)[1].disabled=true;
document.getElementsByName(name)[2].disabled=true;
document.getElementsByName(name)[3].disabled=true;
	if(document.getElementById('DeliberationRes3').value=='')
		{document.getElementById('DeliberationRes3').value=1;}
		else{document.getElementById('DeliberationRes3').value=eval(document.getElementById('DeliberationRes3').value)+1;}}
}
//if(eval(document.getElementById('res0_hidden').value)<eval(document.getElementById('res1_hidden').value)){
//valmax=document.getElementById('res1_hidden').value;
//max='oui';
//}else{valmax=document.getElementById('res0_hidden').value;
//max='non';}
//if(valmax<eval(document.getElementById('res2_hidden').value)){
//valmax=document.getElementById('res2_hidden').value;
//max='abstention';
//}
//if(valmax<eval(document.getElementById('res3_hidden').value)){
//valmax=document.getElementById('res3_hidden').value;
//max='pas de participation';
//}
//if(isNaN(eval(document.getElementById('res0_hidden').value))==true && isNaN(eval(document.getElementById('res1_hidden').value))==false)
//	{valmax=document.getElementById('res1_hidden').value;
//max='oui';}
//if(isNaN(eval(document.getElementById('res1_hidden').value))==true && isNaN(eval(document.getElementById('res0_hidden').value))==false)
//	{valmax=document.getElementById('res0_hidden').value;
//max='non';}

//document.getElementById('res_hidden').value=max;
//document.getElementById('res').value=max;


function vote_global(rep){
var rep = rep;
var mesBoutons = document.getElementsByTagName('input');

for (i = 0; i < mesBoutons.length; i++) {
if(mesBoutons[i].value ==rep){
	mesBoutons[i].checked=true;
	if(rep==0){
		if(document.getElementById('DeliberationRes0').value=='')
		{document.getElementById('DeliberationRes0').value=1;}
		else{document.getElementById('DeliberationRes0').value=eval(document.getElementById('DeliberationRes0').value)+1;}
	}
	if(rep==1){
		if(document.getElementById('DeliberationRes1').value=='')
		{document.getElementById('DeliberationRes1').value=1;}
		else{document.getElementById('DeliberationRes1').value=eval(document.getElementById('DeliberationRes1').value)+1;}

	}
	if(rep==2){
		if(document.getElementById('DeliberationRes2').value=='')
		{document.getElementById('DeliberationRes2').value=1;}
		else{document.getElementById('DeliberationRes2').value=eval(document.getElementById('DeliberationRes2').value)+1;}

	}
	if(rep==3){
		if(document.getElementById('DeliberationRes3').value=='')
		{document.getElementById('DeliberationRes3').value=1;}
		else{document.getElementById('DeliberationRes3').value=eval(document.getElementById('DeliberationRes3').value)+1;}

	}

}


	mesBoutons[i].disabled=true;

}

	if(rep==0){
		document.getElementById('DeliberationRes0').value=eval(document.getElementById('DeliberationRes0').value)-1;}

	if(rep==1){
		document.getElementById('DeliberationRes1').value=eval(document.getElementById('DeliberationRes1').value)-1;}

	if(rep==2){
		document.getElementById('DeliberationRes2').value=eval(document.getElementById('DeliberationRes2').value)-1;}

	if(rep==3){
		document.getElementById('DeliberationRes3').value=eval(document.getElementById('DeliberationRes3').value)-2;}


document.getElementById('DeliberationReset').disabled=false;
}


function clean(){
var mesBoutons = document.getElementsByTagName('input');

for (i = 0; i < mesBoutons.length; i++) {
   mesBoutons[i].disabled=false;

}

document.getElementById('DeliberationRes0').disabled=true;
document.getElementById('DeliberationRes1').disabled=true;
document.getElementById('DeliberationRes2').disabled=true;
document.getElementById('DeliberationRes3').disabled=true;

}

