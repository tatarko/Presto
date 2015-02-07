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
 * Core rendering engine
 * @package presto
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @copyright (c) 2013, Tomas Tatarko
 * @link https://github.com/tatarko/presto
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @since 1.0
 */
class Engine extends Object
{
    /**
     * Escape html tags by default
     * @var boolean
     */
    public $escapeHtml = true;

    /**
     * Active rendering view
     * @var View
     */
    protected $view;

    /**
     * Constructing new rendering engine for given view
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * Renders active view using rendering engine
     * @param boolean $return Returns rendered view instead of printing it to output
     * @return string
     */
    public function render($return = false)
    {
        $filename = $this->view->path;

        if (!file_exists($filename)) {
            throw new Exception(sprintf('Template file %s does not exists', $filename), 404);
        }

        $lastEdit = filectime($filename);
        $cacheFile = $this->view->template->cacheFolder . sprintf('template_%s.php', substr(md5($filename . $lastEdit), 0, 10));

        if (!file_exists($cacheFile)) {
            $this->parseTemplate($filename, $cacheFile);
        }

        $output = $this->renderFileContent($cacheFile, $this->view->values);

        if ($return) {
            return $output;
        }

        echo $output;
    }

    /**
     * Translates source template into executable php file
     * @param string $sourceFile Path to source template file
     * @param string $targetFile Destination path for compiled php file
     */
    protected function parseTemplate($sourceFile, $targetFile)
    {
        $content = file_get_contents($sourceFile);

        $this->parseIncludes($content)
            ->parseConditions($content)
            ->parseCycles($content)
            ->parseVariables($content)
            ->parseSettingVariables($content);

        file_put_contents($targetFile, $content);
    }

    /**
     * Parse `include` directives into php code
     * @param string $content Source template content
     * @return Engine
     * @suports Method-Chaining
     */
    protected function parseIncludes(&$content)
    {
        $pattern = (string) $this->pattern()
                ->put('import')
                ->whitespace()
                ->view()
                ->wrap();

        while (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $partialFile = $this->view->getFilePath($match[1]);

                if (!file_exists($partialFile)) {
                    throw new Exception(sprintf('Template file %s does not exists', $partialFile), 404);
                }

                $content = str_replace($match[0], file_get_contents($partialFile), $content);
            }
        }

        return $this;
    }

    /**
     * Parse variables into php code
     * @param string $content Source template content
     * @return Engine
     * @suports Method-Chaining
     */
    protected function parseVariables(&$content)
    {
        $pattern = $this->pattern()
            ->variable()
            ->filters()
            ->wrap('{{', '}}');

        if (!preg_match_all((string) $pattern, $content, $matches, PREG_SET_ORDER)) {
            return $this;
        }

        foreach ($matches as $match) {
            $content = str_replace($match[0], '<?='
                . ' isset(' . $this->unmaskVariable($match['variable']) . ')'
                . ' ? ' . $this->unmaskVariable($match['variable'], $this->parseInlineFilters($match, 'filters'))
                . ' : "" ?>', $content);
        }

        return $this;
    }

    /**
     * Parse `set` directives into php code
     * @param string $content Source template content
     * @return Engine
     * @suports Method-Chaining
     */
    protected function parseSettingVariables(&$content)
    {
        $pattern = $this->pattern()
            ->put('set')
            ->whitespace()
            ->variable()
            ->whitespace()
            ->put('=')
            ->whitespace()
            ->variable('newValue')
            ->filters()
            ->wrap();

        if (!preg_match_all((string) $pattern, $content, $matches, PREG_SET_ORDER)) {
            return $this;
        }

        foreach ($matches as $match) {
            $content = str_replace($match[0], '<? '
                . $this->unmaskVariable($match['variable'])
                . ' = '
                . $this->unmaskVariable($match['newValue'], $this->parseInlineFilters($match, 'filters'))
                . ' ?>', $content
            );
        }

        return $this;
    }

    /**
     * Parse conditions into php code
     * @param string $content Source template content
     * @return Engine
     * @suports Method-Chaining
     */
    protected function parseConditions(&$content)
    {

        $pattern = $this->pattern()
            ->condition()
            ->whitespace()
            ->variable()
            ->filters()
            ->whitespace()
            ->compare()
            ->whitespace()
            ->variable('comparator', true)
            ->filters('comparatorFilters')
            ->wrap();

        if (!preg_match_all((string) $pattern, $content, $matches, PREG_SET_ORDER)) {
            return $this;
        }

        foreach ($matches as $match) {
            $content = str_replace($match[0], '<? ' . $match['condition'] . '('
                . $this->unmaskVariable($match['variable'], $this->parseInlineFilters($match, 'filters'))
                . (isset($match['compare'], $match['comparator']) ? ' ' . $match['compare'] . ' ' . $this->unmaskVariable($match['comparator'], $this->parseInlineFilters($match, 'comparatorFilters')
                    ) : '') . ') : ?>'
                , $content);
        }

        $content = preg_replace(
            (string) $this->pattern()->put('else')->wrap(), '<? else : ?>', preg_replace(
                (string) $this->pattern()->put('endif')->wrap(), '<? endif ?>', $content
            )
        );

        return $this;
    }

    /**
     * Parse `for` directives into php code
     * @param string $content Source template content
     * @return Engine
     * @suports Method-Chaining
     */
    protected function parseCycles(&$content)
    {
        $pattern = $this->pattern()
            ->put('for ')
            ->variable('value')
            ->put(' in ')
            ->variable()
            ->filters()
            ->wrap();

        if (!preg_match_all((string) $pattern, $content, $matches, PREG_SET_ORDER)) {
            return $this;
        }

        foreach ($matches as $match) {
            $content = str_replace($match[0], '<? if(isset(' . $this->unmaskVariable($match['variable']) . ')'
                . ' && is_array(' . $this->unmaskVariable($match['variable']) . ')) :'
                . PHP_EOL . '$thisCount = count(' . $this->unmaskVariable($match['variable']) . ');'
                . PHP_EOL . '$thisPosition = 0;'
                . PHP_EOL . 'foreach(' . $this->unmaskVariable($match['variable'], $this->parseInlineFilters($match, 'filters'))
                . ' as $thisKey => ' . $this->unmaskVariable($match['value']) . ') :'
                . PHP_EOL . '	++$thisPosition;'
                . PHP_EOL . '	$thisIsEven = $thisPosition % 2 == 1;'
                . PHP_EOL . '	$thisIsEven = $thisPosition % 2 == 1;'
                . PHP_EOL . '	$thisIsOdd = $thisPosition % 2 == 0;'
                . PHP_EOL . '	$thisIsFirst = $thisPosition == 1;'
                . PHP_EOL . '	$thisIsLast = $thisPosition == $thisCount; ?>', $content);
        }

        $content = preg_replace(
            (string) $this->pattern()->put('endfor')->wrap(), '<? endforeach; unset($thisKey, $thisPosition, $thisCount, $thisIsEven, $thisIsOdd, $thisIsFirst, $thisIsLast); endif; ?>', $content
        );

        return $this;
    }

    /**
     * Parse inline filters into php code
     * @param array $row Marches from other preg_match call
     * @param string $name Index that filter name is stored under
     * @param boolean $addBasic Add basic list of filters?
     * @return array
     * @internal
     */
    protected function parseInlineFilters(array $row, $name, $addBasic = true)
    {
        $filters = array();

        if (!isset($row[$name])) {
            return $filters;
        }

        if (preg_match_all('#\|(?P<name>[a-zA-Z]+)(\((?P<args>.+)\))?#', $row[$name], $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $filters[$match['name']] = isset($match['args']) ? $match['args'] : null;
            }
        }

        return $addBasic ? $this->addBasicFilters($filters) : $filters;
    }

    /**
     * Callback for adding basic filters
     * @param array $filters List of input filters
     * @return array List of output filters
     * @internal
     */
    protected function addBasicFilters(array $filters)
    {
        if ($this->escapeHtml && !isset($filters['raw'])) {
            $filters['escape'] = null;
        }

        return $filters;
    }

    /**
     * Makes new instance of PatternBuilder
     * @return PatternBuilder
     */
    protected function pattern()
    {
        return new PatternBuilder;
    }

    /**
     * Unmask matched variable pointer
     * @param string $variable Name of the variable to unmask
     * @param array $filters List of filters to apply on variable
     * @return string
     */
    protected function unmaskVariable($variable, array $filters = array())
    {
        if (in_array(substr($variable, 0, 1), array('"', '\''))) {
            $pattern = $variable;
        } else {
            $variable = explode('.', $variable);
            $pattern = '$' . current($variable);

            foreach (array_slice($variable, 1) as $part) {
                $pattern .= sprintf('["%s"]', $part);
            }
        }

        if (empty($filters)) {
            return $pattern;
        }

        $indent = 0;
        unset($filters['raw']);
        foreach ($filters as $name => $args) {
            $pattern = sprintf(
                '%s%sPrestoEngine\\Helper::%s(%s%s%s)%s', PHP_EOL, str_repeat("\t", count($filters) - ++$indent), $name, $pattern, $args === null ? '' : ', ', $args, PHP_EOL
            );
        }

        return $pattern;
    }

    /**
     * Compiling executable php code (rendering template)
     * @param string $__fileToInclude Path to the executable php file
     * @param array $__valuesToUse Scope to use
     * @return string Rendered view
     */
    protected function renderFileContent($__fileToInclude, array $__valuesToUse)
    {
        ob_start();
        extract($__valuesToUse);
        require $__fileToInclude;
        return ob_get_clean();
    }
}
