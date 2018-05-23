<?php

namespace KRG\CmsBundle\Util;

class Str
{
    public static function underscoreCase($string)
    {
        $string = strtr(utf8_decode($string), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
        $string = preg_replace('/[^a-z0-9]+/i', ' ', $string);
        $string = trim($string);
        $string = str_replace(' ', '_', $string);
        $string = strtolower($string);

        return $string;
    }
}
