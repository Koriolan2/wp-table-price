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
    /**
     * Функція для копіювання тексту в буфер обміну.
     * @param {string} text - Текст, який потрібно скопіювати.
     * @param {object} buttonElement - Елемент кнопки, текст якої оновлюватиметься.
     */
    function copyToClipboard(text, buttonElement) {
        navigator.clipboard.writeText(text).then(function() {
            // Оновлюємо текст кнопки після успішного копіювання
            $(buttonElement).text('Copied!');
            setTimeout(function() {
                $(buttonElement).text('Copy');
            }, 2000); // Повертаємо текст кнопки назад через 2 секунди
        }).catch(function(error) {
            console.error('Failed to copy text: ', error);
        });
    }

    /**
     * Обробник для кнопки копіювання в метабоксі.
     */
    $('#copy_shortcode_button').on('click', function() {
        var shortcodeField = $('#table_price_shortcode').val();
        copyToClipboard(shortcodeField, this); // Використовуємо загальну функцію копіювання
    });

    /**
     * Делегований обробник для кнопок копіювання в таблиці.
     */
    $(document).on('click', '.copy_shortcode_button', function() {
        // Отримуємо значення шорткоду з сусіднього поля input
        var shortcodeField = $(this).prev('input').val();
        copyToClipboard(shortcodeField, this); // Використовуємо загальну функцію копіювання
    });

    /**
     * Обробник для відображення поля "Rows per Page" в налаштуваннях DataTable.
     * Поле показується тільки якщо увімкнено пагінацію.
     */
    $('#table_price_settings_paging').on('change', function() {
        if ($(this).is(':checked')) {
            $('#rows_per_page_wrapper').show();
        } else {
            $('#rows_per_page_wrapper').hide();
        }
    });
});
