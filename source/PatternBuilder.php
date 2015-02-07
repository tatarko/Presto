<?php

namespace PrestoEngine;

/**
 * Builder regexp patternov
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 */
class PatternBuilder
{
    /**
     * Pattern na odchytavanie nazvu premennych
     */
    const PATTERN_VARIABLE = '(?P<:::::>\'[^\']*\'|"[^"]*"|[a-zA-Z0-9.]+)';

    /**
     * Pattern na odchytavanie filtrov
     */
    const PATTERN_FILTERS = '(?P<:::::>\|.+)';

    /**
     * Pattern na filtrovanie whitespacov
     */
    const PATTERN_WHITESPACE = '[ \t\r\n\v\f]*';

    /**
     * Pattern na zachytavanie porovnavacich znamienok
     */
    const PATTERN_COMPARE = '(?P<:::::>\=|\>\=|\<\=|\!\=)';

    /**
     * Pattern na urcovanie, aku podmienku riesime
     */
    const PATTERN_CONDITION = '(?P<:::::>if|elseif)';

    /**
     * Pattern na odchytavanie nazvu viewu na importovanie
     */
    const PATTERN_VIEW = '(?P<:::::>[a-zA-Z0-9/]+)';

    /**
     * Aktualny pattern
     * @var string
     */
    public $pattern = '';

    /**
     * Zachyti premennu
     * @param string $name Nazov odchytavaneho indexu
     * @param string $optional Je to len volitelny odchyt?
     * @return \PrestoEngine\PatternBuilder
     */
    public function variable($name = 'variable', $optional = false)
    {
        $this->pattern .= str_replace(':::::', $name, self::PATTERN_VARIABLE);
        $this->pattern .= $optional ? '?' : '';
        return $this;
    }

    /**
     * Zachytenie sposobu podmienky (if/elseif)
     * @param string $name Nazov odchytavaneho indexu
     * @param string $optional Je to len volitelny odchyt?
     * @return \PrestoEngine\PatternBuilder
     */
    public function condition($name = 'condition', $optional = false)
    {
        $this->pattern .= str_replace(':::::', $name, self::PATTERN_CONDITION);
        $this->pattern .= $optional ? '?' : '';
        return $this;
    }

    /**
     * Zachyti view
     * @param string $name Nazov odchytavaneho indexu
     * @param string $optional Je to len volitelny odchyt?
     * @return \PrestoEngine\PatternBuilder
     */
    public function view($name = 'view', $optional = false)
    {
        $this->pattern .= str_replace(':::::', $name, self::PATTERN_VIEW);
        $this->pattern .= $optional ? '?' : '';
        return $this;
    }

    /**
     * Zachyti filtre premennej
     * @param string $name Nazov odchytavaneho indexu
     * @param string $optional Je to len volitelny odchyt?
     * @return \PrestoEngine\PatternBuilder
     */
    public function filters($name = 'filters', $optional = true)
    {
        $this->pattern .= str_replace(':::::', $name, self::PATTERN_FILTERS);
        $this->pattern .= $optional ? '?' : '';
        return $this;
    }

    /**
     * Zachyti filtre premennej
     * @param string $name Nazov odchytavaneho indexu
     * @param string $optional Je to len volitelny odchyt?
     * @return \PrestoEngine\PatternBuilder
     */
    public function compare($name = 'compare', $optional = true)
    {
        $this->pattern .= str_replace(':::::', $name, self::PATTERN_COMPARE);
        $this->pattern .= $optional ? '?' : '';
        return $this;
    }

    /**
     * Zachyti premennu
     * @return \PrestoEngine\PatternBuilder
     */
    public function whitespace()
    {
        $this->pattern .= self::PATTERN_WHITESPACE;
        return $this;
    }

    /**
     * Obali pattern do hranicnych tagov
     * @param string $start Cim ma pattern zacinat
     * @param string $end Cim ma pattern koncit
     * @return \PrestoEngine\PatternBuilder
     */
    public function wrap($start = '{%', $end = '%}')
    {
        $this->pattern = preg_quote($start)
            . self::PATTERN_WHITESPACE
            . $this->pattern
            . self::PATTERN_WHITESPACE
            . preg_quote($end);
        return $this;
    }

    /**
     * Co ma pattern povinne obsahovat?
     * @param string $string
     * @return \PrestoEngine\PatternBuilder
     */
    public function put($string)
    {
        $this->pattern .= preg_quote($string);
        return $this;
    }

    /**
     * Konvertovanie objektu na string, vrateny samotny pattern
     * @return string
     */
    public function __toString()
    {
        return sprintf('#%s#', $this->pattern);
    }
}