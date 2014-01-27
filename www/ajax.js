
$(function() {
    $("input[type='checkbox']").change(checkActualProperties);
    checkActualProperties();
});

/**
 * Функция проверки доступных для отметки атрибутов.
 * Посылает Ajax запрос со всеми отмеченными в форме чекбоксами.
 * В ответ получает JSON с IDшниками нужных полей.
 * Дизейблит те, которые не получила.
 *
 * @return void
 */
function checkActualProperties() {
    $.ajax('/get-actual-properties', {
        dateType: 'json',
        data: $("#form").serialize(),
        success: function(data) {
            $("input[type='checkbox']").each(function() {
                if (!in_array($(this).val(), data.ids)) {
                    $(this).prop("disabled", true);
                } else {
                    $(this).prop("disabled", false);
                }
            });
        }
    });
}

/**
 * Аналог PHP функции php.net/in_array
 *
 * @param string needle
 * @param array haystack
 * @param string strict
 * @returns {boolean}
 */
function in_array(needle, haystack, strict) {
    var found = false, key, strict = !!strict;

    for (key in haystack) {
        if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
            found = true;
            break;
        }
    }

    return found;
}