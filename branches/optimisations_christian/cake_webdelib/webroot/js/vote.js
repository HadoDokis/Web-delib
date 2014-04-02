window.onload=initAffichage;

function initAffichage() {
	// Saisie des présents masquée
	$('#VoteListePresents').val(1);
	affichageListePresents($('#VoteListePresents'));

	// Affichage du type de saisie du vote
	selectElement = document.getElementById("VoteTypeVote");
	selectElement.value=1;
	affichageTypeVote(selectElement);

	// Intialisation du décompte des voix
	majTotauxVotes();
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
	majTotauxVotes();
}

/**
 * gestion des raccourcis pour le vote
 */
function vote_global(obj, scope){
	var name = $(obj).attr('name');
	var valChecked = $('#tableDetailVote input[type=radio][name='+name+']:checked').val();
	if (scope == 'tous') {
		$('#tableDetailVote input[type=radio][name^=racc_typeacteur][value='+valChecked+']').attr('checked', 'checked');
                $('#tableDetailVote input[type=radio][name^=data][value='+valChecked+']').attr('checked', 'checked');
	} else {
                $('#tableDetailVote tr.'+scope+' input[type=radio][name^=data][value='+valChecked+']').attr('checked', 'checked');
                $('#tableDetailVote input[type=radio][name=racc_tous]:checked').removeAttr('checked');
	}
	majTotauxVotes();
}

/**
 * mise à jour des totaux des votes
 */
function majTotauxVotes() {
	$('#VoteRes3').val($('#tableDetailVote input[type=radio][name^=data][value=3]:checked').length);
        $('#VoteRes2').val($('#tableDetailVote input[type=radio][name^=data][value=2]:checked').length);
        $('#VoteRes4').val($('#tableDetailVote input[type=radio][name^=data][value=4]:checked').length);
        $('#VoteRes5').val($('#tableDetailVote input[type=radio][name^=data][value=5]:checked').length);
}
