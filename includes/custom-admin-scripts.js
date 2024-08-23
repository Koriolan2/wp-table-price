jQuery(document).ready(function($) {
    $('#table_price_settings_paging').on('change', function() {
        if ($(this).is(':checked')) {
            $('#rows_per_page_wrapper').show();
        } else {
            $('#rows_per_page_wrapper').hide();
        }
    });
});

jQuery(document).ready(function($) {
    $('#copy_shortcode_button').on('click', function() {
        // Створюємо тимчасове текстове поле для копіювання
        var tempInput = document.createElement('input');
        tempInput.value = $('#table_price_shortcode').val();
        document.body.appendChild(tempInput);

        // Вибираємо текст і копіюємо його в буфер обміну
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // Для мобільних пристроїв
        document.execCommand('copy');
        document.body.removeChild(tempInput); // Видаляємо тимчасовий елемент

        // Оновлюємо текст кнопки після копіювання
        $('#copy_shortcode_button').text('Copied!');
        setTimeout(function() {
            $('#copy_shortcode_button').text('Copy');
        }, 2000); // Повертаємо текст кнопки назад через 2 секунди
    });
});


