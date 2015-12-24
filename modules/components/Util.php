<?php
/**
 * Created by PhpStorm.
 * User: feb
 * Date: 23/12/15
 * Time: 09.33
 */

namespace febfeb\dynamicfield\modules\components;


class Util
{
    public static function slugifyToDbSafe($text)
    {
        return str_replace("-", "_", Util::slugify($text));
    }

    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}