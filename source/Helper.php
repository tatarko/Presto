<?php

/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2013 Tomáš Tatarko
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace tatarko\presto;

/**
 * Local helper class
 * @package presto
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @copyright (c) 2013, Tomas Tatarko
 * @link https://github.com/tatarko/presto
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @since 1.0
 */
class Helper
{
    /**
     * Formats input date/time into requrested format
     * @param string $value Any date/time format that can be strtotime-ed
     * @param string $format Requested output format
     * @return string
     */
    public static function date($value, $format = 'd.m.Y H:i:s')
    {
        return date($format, is_numeric($value) ? $value : strtotime($value));
    }

    /**
     * Escape html markup to html entities
     * @param string $value Input markup
     * @return string Encoded string
     */
    public static function escape($value)
    {
        return htmlspecialchars($value);
    }

    /**
     * Gets absolute value of input number
     * @param float $value
     * @return float
     */
    public static function abs($value)
    {
        return abs($value);
    }

    /**
     * Capitalizes first letter of input string
     * @param string $value
     * @return string
     */
    public static function capitalize($value)
    {
        return ucfirst($value);
    }

    /**
     * Modifying input date (adds\subs intervals)
     * @param string $value Any date/time format that can be strtotime-ed
     * @param string $how Any format that can be strtotime-ed
     * @return string
     */
    public static function modifyDate($value, $how)
    {
        return strtotime($how, is_numeric($value) ? $value : strtotime($value));
    }

    /**
     * Gets default value in case that input value is null-like
     * @param string $value
     * @param string $default
     * @return string
     */
    public static function byDefault($value, $default = '')
    {
        return $value ?: $default;
    }

    /**
     * Join array into single string using given separator
     * @param array $value Input array
     * @param string $separator Separator used for concating input values
     * @return string
     */
    public static function join($value, $separator = ', ')
    {
        return is_array($value) ? implode($separator, $value) : $value;
    }

    /**
     * Encodes input value as json
     * @param mixed $value Input variable
     * @return string
     */
    public static function json($value)
    {
        return json_encode($value);
    }

    /**
     * Vrati pocet prvkov daneho pola alebo dlzku retazca
     * @param array|string $value Vstupna hodnota
     * @return int
     */
    public static function length($value)
    {
        return is_array($value) ? count($value) : mb_strlen($value);
    }

    /**
     * Lowercase input string
     * @param string $value
     * @return string
     */
    public static function lower($value)
    {
        return mb_strtolower($value);
    }

    /**
     * Formats number into requested format
     * @param float $value Input number
     * @param int $decimals Decimals length (number will be rounded if longer)
     * @param string $decimalSeparator String used for separating decimals from  integers
     * @param string $thousandsSeparator String used for separating thousands, milions, ...
     * @return string
     */
    public static function numberFormat($value, $decimals = 2, $decimalSeparator = ',', $thousandsSeparator = ' ')
    {
        return number_format($value, $decimals, $decimalSeparator, $thousandsSeparator);
    }

    /**
     * Uppercase input string
     * @param string $value
     * @return string
     */
    public static function upper($value)
    {
        return mb_strtoupper($value);
    }

    /**
     * Removes html markup from input string
     * @param string $value
     * @return string
     */
    public static function stripTags($value)
    {
        return strip_tags($value);
    }

    /**
     * Capitalizes first letters of all of the input words
     * @param string $value
     * @return string
     */
    public static function title($value)
    {
        return ucwords($value);
    }

    /**
     * Removes whitespaces from start and end of the string
     * @param string $value
     * @param string $what
     * @return string
     */
    public static function trim($value, $what = ' ')
    {
        return trim($value, $what);
    }

    /**
     * Escapes input string as url
     * @param string $value
     * @return string
     */
    public static function urlEncode($value)
    {
        return urlencode($value);
    }
}
