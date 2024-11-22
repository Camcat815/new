jQuery(document).ready(function($) {
    $('.city-autocomplete, .location-autocomplete').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: listeo_core.ajax_url,
                dataType: "json",
                data: {
                    action: 'get_city_suggestions',
                    term: request.term,
                    nonce: listeo_core.ajax_nonce
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 2
    });
});