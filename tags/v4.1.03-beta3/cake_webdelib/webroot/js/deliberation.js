function updateTypeseances(domObj) {
    var ajaxUrl = '/deliberations/getTypeseancesParTypeacteAjax/'+$(domObj).val();
    $.ajax({
            url: ajaxUrl,
            beforeSend: function() {
                   $('#selectTypeseances').html('');
                   $('#selectDatesSeances').html('');
            },
            success: function(result) {
                    $('#selectTypeseances').html(result);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert(textStatus);
            }
    });
}

function updateDatesSeances(domObj) {
    var ajaxUrl = '/deliberations/getSeancesParTypeseanceAjax/'+$(domObj).val() ;
    $.ajax({
            url: ajaxUrl,
            beforeSend: function() {
                   $('#selectDatesSeances').html('');
            },
            success: function(result) {
                   $('#selectDatesSeances').html(result);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                   alert(textStatus);
            }
    });
}
