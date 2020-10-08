$(document).ready(function(){
    $("#scheme").change(function() {
        var scheme_type_id = $(this).children("option:selected").val();
        var scheme_merchant_id = $("#scheme-merchant-id").val();

        $(".position-wrapper").html("");

        $.get(base_url + "income-schemes/get-merchants-in-scheme/" + scheme_type_id + "/" + scheme_merchant_id, function( html ) {
            $(".position-wrapper").html(html);
        });
    });
});