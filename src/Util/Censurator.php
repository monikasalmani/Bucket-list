<?php

namespace App\Util;

class Censurator
{
    const UNWANTED_WORDS = ["pif", "paf", "pouf"];

    public function purify(string $text): string
    {
        //peut être fait en une ligne si on ne veut pas remplacer par un nombre précis d'*
        foreach (self::UNWANTED_WORDS as $unwantedWord) {
            //autant d'* que de lettres dans le mot :
            $replacement = str_repeat("*", mb_strlen($unwantedWord));
            $text = str_ireplace($unwantedWord, $replacement, $text);
        }
        return $text;
    }
}