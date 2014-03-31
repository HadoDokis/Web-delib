$(document).ready(function(){
    changeProtocol();
});
function changeActivation(element) {
    if ($(element).val() == 'true') {
        $('#config_content').show();
    } else {
        $('#config_content').hide();
    }
}

function changeProtocol() {
    var protocol = $('#ConnecteurTdtProtocol').val();
    if (protocol == 'pastell') {
        $('.pastell-infos').show();
        $('#ConnecteurHost').val($('#ConnecteurPastellHost').val());
    } else {
        $('.pastell-infos').hide();
    }
    if (protocol == 's2low') {
        $('.s2low-infos').show();
        $('#ConnecteurHost').val($('#ConnecteurS2lowHost').val());
    } else {
        $('.s2low-infos').hide();
    }
    $('#ConnecteurHost').prop('placeholder', 'https://'+protocol+'.x.x.org');
}
