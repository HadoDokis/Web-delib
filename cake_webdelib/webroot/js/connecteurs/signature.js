function changeActivation(element) {
    if ($(element).val() == 'true') {
        $('#config_content').show();
    } else {
        $('#config_content').hide();
    }
}

function changeProtocol() {
    var protocol = $('#ConnecteurSignatureProtocol').val();
    if (protocol == 'pastell') {
        $('#pastell_type').show();
    } else {
        $('#pastell_type').hide();
    }
    if (protocol == 'iparapheur') {
        $('#infos_certificat').show();
    } else {
        $('#infos_certificat').hide();
    }
    $('#ConnecteurHost').prop('placeholder', 'http://'+protocol+'.x.x.org');
}
