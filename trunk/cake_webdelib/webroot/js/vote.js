$(document).ready(function() {
    // Saisie des présents masquée
    $('#VoteListePresents').val(1);
    affichageListePresents();

    // Affichage du type de saisie du vote
    $("#VoteTypeVote").val(1);
    affichageTypeVote();

    // Intialisation du décompte des voix
    majTotauxVotes();
    
    // Listeneur sur le select onchange
    $( "#VoteTypeVote" ).change(function() {
        affichageTypeVote();
    });
    
    // Listeneur comptant le nombre de caractéres sur le textarea commentaire
    $('#charLeft').append($('#DeliberationVoteCommentaire').val().length);
    $('#DeliberationVoteCommentaire').keyup(function() {
        var len = this.value.length;
        if (len >= 500) {
            this.value = this.value.substring(0, 500);
        }
        $('#charLeft').text(this.value.length);
    });
    
    //affiche ou cache la liste des mendataires selon si l'élu est present ou pas
    $( "#DeliberationListerPresentsForm input:checkbox" ).click(function() {
        $thisShow = $('#' + $(this).attr('id') + ':checked').val();
        if( $thisShow == 1 )
        {
            $('#liste_'+$(this).attr('id')).select2("enable", false);
            $('#liste_'+$(this).attr('id')).select2("val", "");
        }
        else
        {
            $('#liste_'+$(this).attr('id')).select2("enable", true);
        }
    });
});
    
function affichageTypeVote() {
    $('#voteDetail, #voteTotal, #voteResultat, #votePrendsActe').hide();
    switch ($("#VoteTypeVote").val()) {
        case '1':
            $('#voteDetail').show();
            break;
        case '2':
            $('#voteTotal').show();
            break;
        case '3':
            $('#voteResultat').show();
            break;
        case '4':
            $('#votePrendsActe').show();
            break;
    }
}

function affichageListePresents() {
    switch ($("#VoteListePresents").val()) {
        case '2':
            $('#saisiePresents').show();
            break;
        default:
            $('#saisiePresents').hide();
            break;
    }
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
        console.log('Tous : ' + valChecked)
        $('#tableDetailVote input[type=radio][name^=racc_typeacteur][value='+valChecked+']').attr('checked', 'checked');
        $('#tableDetailVote input[type=radio][name^=data][value='+valChecked+']').attr('checked', 'checked');
    } else {
        console.log('Juste un : ' + valChecked)
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


$(".select2.selectone").select2({
    width: "element",
    allowClear: true,
    dropdownCssClass: "selectMaxWidth",
    dropdownAutoWidth: true,
    placeholder: "Selectionnez un élément",
    formatSelection: function (object, container) {
        // trim sur la sélection (affichage en arbre)
        return $.trim(object.text);
    }
});
