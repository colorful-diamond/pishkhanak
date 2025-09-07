<?php

function clear_quotes($string)
{
    return str_replace(['"', "'"], '', $string);
}

function fa2en($string)
{
    return \App\Helpers\NumberConverter::toEnglish($string);
}


