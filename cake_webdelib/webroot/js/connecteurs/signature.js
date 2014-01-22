function changeActivation(element) {
    if ($(element).val() == 'true') {
        $('#config_content').show();
    } else {
        $('#config_content').hide();
    }
}

function changeProtocol() {
    var protocol = $('#ConnecteurSignatureProtocol').val();
    if (protocol == 'iparapheur') {
        $('#infos_certificat').show();
    } else {
        $('#infos_certificat').hide();
    }
    var hidden = 'Connecteur' + capitaliseFirstLetter(protocol);
    $("#ConnecteurHost").val($('#' + hidden + 'Host').val());
    $("#ConnecteurLogin").val($('#' + hidden + 'Login').val());
    $("#ConnecteurPwd").val($('#' + hidden + 'Pwd').val());
    $("#ConnecteurType").val($('#' + hidden + 'Type').val());
}

function capitaliseFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}