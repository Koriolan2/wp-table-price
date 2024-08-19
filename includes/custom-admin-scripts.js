jQuery(document).ready(function($) {
    $('#table_price_settings_paging').on('change', function() {
        if ($(this).is(':checked')) {
            $('#rows_per_page_wrapper').show();
        } else {
            $('#rows_per_page_wrapper').hide();
        }
    });
});
