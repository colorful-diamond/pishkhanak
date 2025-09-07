<?php

namespace App\Services;

/* PERSIAN_COMMENT */
    public static function mb_str_split($string, $string_length = 1) {
        if (mb_strlen($string) > $string_length || !$string_length) {
            do {
                $parts[] = mb_substr($string, 0, $string_length);
                $string  = mb_substr($string, $string_length);
            } while (!empty($string));
        } else {
            $parts = array($string);
        }
        return $parts;
    }
}
