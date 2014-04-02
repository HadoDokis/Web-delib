function changeActivation(element) {
    if ($(element).val() == 'true') {
        $('#config_content').show();
    } else {
        $('#config_content').hide();
    }
}

function changeActivationCert(element) {
    if ($(element).val() == 'true') {
        $('#idelibre_cert').show();
    } else {
        $('#idelibre_cert').hide();
    }
}
