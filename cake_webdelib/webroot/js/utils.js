window.onload = function () {
    afficheOngletEnErreur();
};

function montre(id) {
    var d = document.getElementById(id);
    for (var i = 1; i <= 15; i++) {
        if (document.getElementById('smenu' + i)) {
            document.getElementById('smenu' + i).style.display = 'none';
        }
    }
    if (d) {
        d.style.display = 'block';
    }
}

function OuvrirFenetre(url, nom, detail) {
    var w = window.open(url, nom, detail);
}

function FermerFenetre() {
    var choix = confirm("Voulez-vous fermer la fenetre ?");
    if (choix)  window.close();
}
function FermerFenetre2() {
    var choix = confirm("Voulez-vous fermer la fenetre ?");
    if (choix)  history.back();
}
function returnChoice(text, arg) {

    var1 = 'classif1';
    var2 = 'num_pref';
    elt1 = window.opener.document.getElementById(var1);
    elt2 = window.opener.document.getElementById(var2);

    if (text) {
        elt1.value = text;
        elt2.value = arg;
    } else {
        elt1.value = '';
        elt2.value = '';
    }
    window.close();
}

function return_choice_lot(text, arg, delibId) {

    var1 = delibId + 'classif1';
    var2 = delibId + 'classif2';
    elt1 = window.opener.document.getElementById(var1);
    elt2 = window.opener.document.getElementById(var2);
    if (text) {
        elt1.value = text;
        elt2.value = arg;
    } else {
        elt1.value = '';
        elt2.value = '';
    }
    window.close();
}

function disable(id, val) {
    if (val == 1)
        document.getElementById(id).disabled = true;
    else
        document.getElementById(id).disabled = false;
}


function changeService(params) {
    var url = params.id + "services/changeService/" + params.value;
    document.location = url;
}


function add_field(num) {

    var a = document.getElementById('lien_annexe');
    a.firstChild.nodeValue = 'Joindre une autre annexe';

    if (navigator.appName == 'Microsoft Internet Explorer') {
        var br = document.createElement('br');
        var d = document.getElementById('cible' + num);
        var p = document.createElement("p");
        d.appendChild(p);
        var n = d.childNodes.length;
        p.id = n;
    }

    else {
        var d = document.getElementById('cible' + num);
        var p = document.createElement("p");
        var n = d.childNodes.length;
        var br = document.createElement('br');
        p.id = n;
        d.appendChild(p);
    }

    var div2 = document.createElement('div');
    var input2 = document.createElement('input');
    input2.id = 'AnnexeFile_' + n;
    input2.type = 'file';
    input2.size = '40';
    input2.name = num + '_file_' + n;
    div2.appendChild(input2);
    p.appendChild(div2);

//	var link = document.createElement('a');
//	link.id = 'Lien_'+n;
//	link.href = 'javascript:del_field('+n+')';
//	text = document.createTextNode('Supprimer');
//	link.appendChild(text);
//	p.appendChild(br);
//	p.appendChild(link);

    document.getElementById('cible' + num).style.visibility = 'visible';
}

function del_field(node) {
    var node = document.getElementById(node);
    var parent = node.parentNode;
    parent.removeChild(node);


//changement du texte du lien lorsqu'il ne reste plus d'annexe

    var d = document.getElementById('cible');
    var n = d.childNodes.length;

    if (n < 2) {
        var a = document.getElementById('lien_annexe');
        a.firstChild.nodeValue = 'Joindre une annexe';
    }
}

/******************************************************************************/
/* Gestion des onglets pour l'affichage des informations supplémentaires      */
/******************************************************************************/
function afficheOnglet(nOnglet) {
    for (var i = 1; i <= 10; i++) {
        divTab = document.getElementById('tab' + i);
        lienTab = document.getElementById('lienTab' + i);
        if (divTab && lienTab) {
            if (i == nOnglet) {
                divTab.style.display = '';
                lienTab.className = 'ongletCourant';
            } else {
                divTab.style.display = 'none';
                lienTab.className = '';
            }
        }
    }
}

/******************************************************************************/
/* Fonction de suppression d'une information supplémentaire de type fichier   */
/******************************************************************************/
function infoSupSupprimerFichier(infoSupCode, titre) {
    /* Masque le nom du fichier et les liens */
    document.getElementById(infoSupCode + 'AfficheFichier').style.display = 'none';
    /* Affiche le span pour l'affichage de l'input */
    var sInput = document.getElementById(infoSupCode + 'InputFichier');
    sInput.style.display = '';
    /* Creation de l'input file */
    var inputFichier = document.createElement('input');
    inputFichier.type = 'file';
    inputFichier.id = 'Infosup' + infoSupCode;
    inputFichier.name = 'data[Infosup][' + infoSupCode + ']';
    inputFichier.title = titre;
    inputFichier.size = '60';
    /* Ajoute l'input file au span */
    sInput.appendChild(inputFichier);
}

/******************************************************************************/
/* Fonction de suppression d'un fichier joint                                 */
/******************************************************************************/
function supprimerFichierJoint(modele, champ, titre) {
    /* Masque le nom du fichier et les liens */
    document.getElementById(modele + champ + 'AfficheFichierJoint').style.display = 'none';
    /* Affiche le span pour l'affichage de l'input */
    var sInput = document.getElementById(modele + champ + 'InputFichierJoint');
    sInput.style.display = '';
    /* Creation de l'input file */
    var inputFichier = document.createElement('input');
    inputFichier.type = 'file';
    inputFichier.id = modele + champ;
    if (modele == "Deliberation") champ += "_upload";
    inputFichier.name = 'data[' + modele + '][' + champ + ']';
    inputFichier.title = titre;
    $(inputFichier).addClass('file-text');
    inputFichier.onChange = 'changeFichierTexte(this)';
    /* Ajoute l'input file au span */
    sInput.appendChild(inputFichier);
    /* Ajoute le bouton Effacer */
    var file_input_index = 1;
    while ($("#file_input_container_" + file_input_index).length !== 0) file_input_index++;
    $(inputFichier).wrap('<div id="file_input_container_' + file_input_index + '"></div>');
    $(inputFichier).after('<a href="javascript:void(0)" class="purge_file btn btn-mini btn-danger"  onclick="resetUpload(\'file_input_container_' + file_input_index + '\')"><i class="fa fa-eraser"></i> Effacer</a>');
    $(inputFichier).change(function(){
        if ($(this).val() != '') {
            var tmpArray = $(this).val().split('.');
            //Test sur l'extension (ODT ?)
            var extension = tmpArray[tmpArray.length - 1];
            if (extension.toLowerCase() != 'odt') {
                $.jGrowl("Format du document invalide. Seuls les fichiers au format ODT sont autorisés.", {header: "<strong>Erreur :</strong>"});
                $(this).val(null);
                return false;
            }
            //Test sur le nom de fichier (>75car)
            var tmpArray = $(this).val().split('\\');
            var filename = tmpArray[tmpArray.length - 1];
            if (filename.length > 75){
                $.jGrowl("Le nom du fichier ne doit pas dépasser 75 caractères.", {header: "<strong>Erreur :</strong>"});
                $(this).val(null);
                return false;
            }
        }
    });
}

/**
 * Vide la valeur (fichier selectionné) de tous les champs input[type="file"] appartenant au container
 * @param string containerId
 */
function resetUpload(containerId){
    $('#'+containerId+' input[type="file"]').val(null);
}

/******************************************************************************/
/* Fonction pour eviter les doubles click                                     */
/******************************************************************************/
var clicAutorise = true;
function disableDiv(nameDiv) {
    if (clicAutorise) {
        var targetElement;
        var loadingElement;
        targetElement = document.getElementById(nameDiv);
        loadingElement = document.getElementById('loading');
        if (targetElement.style.display == "none") {
            targetElement.style.display = "";
            loadingElement.style.display = "none";
        } else {
            targetElement.style.display = "none";
            loadingElement.style.display = "";
        }
    }
    clicAutorise = false;
}

/******************************************************************************/
/* Affichage le premier onglets qui comporte un objet de class classError     */
/******************************************************************************/
function afficheOngletEnErreur() {
    var classErreur = "error-message";
    for (var iTab = 1; iTab <= 10; iTab++) {
        divTab = document.getElementById('tab' + iTab);
        if (divTab) {
            var divs = divTab.getElementsByTagName("div");
            for (var j = 0; j < divs.length; j++) {
                if (divs[j].className == classErreur) {
                    afficheOnglet(iTab);
                    return;
                }
            }
        } else {
            return;
        }
    }
}

function changeClassification() {
    window.open('/deliberations/classification', 'Select_attribut', 'scrollbars=yes,width=570,height=450');
}

function resetClassification() {
    $("#classif1").val('');
    return false;
}