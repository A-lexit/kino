<?php


if (!function_exists('pluralize_results')) {
    /**
     * Повертає правильну форму слова "збіг" залежно від кількості
     *
     * @param int $count Кількість результатів
     * @return string
     */
    function pluralize_results($count) {
        if ($count === 1) {
            return 'збіг';
        }

        return 'збігів';
    }

}
