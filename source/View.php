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
 * Basic view interfacing and pattern for layout/menu view
 * @package presto
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @copyright (c) 2013, Tomas Tatarko
 * @link https://github.com/tatarko/Presto
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @since 1.0
 * @property-read string $path Path to the real view file
 * @property-read Presto\Template $template Instance of template that view belongs to
 */
class View extends Object
{
    /**
     * Name of the view
     * @var string
     */
    public $name;

    /**
     * Path to the real view file
     * @var string
     */
    protected $path;

    /**
     * Variables for rendering the view
     * @var array
     */
    public $values = array();

    /**
     * Instance of template that view belongs to
     * @var Presto\Template
     */
    protected $template;

    /**
     * Construction of the view
     * @param Presto\Template $template Instance of template that view belongs to
     * @param string $name Name of the view
     * @param array $values Variables for rendering the view
     */
    public function __construct(Template $template, $name, array $values = array())
    {
        $this->name = $name;
        $this->template = $template;
        $this->path = $this->getFilePath($name);
        $this->values += $values;
    }

    /**
     * Translates name of the view to the real file path
     * @param string $name Name of the view
     * @return string
     */
    public function getFilePath($name)
    {
        return sprintf('%sviews/%s.tpl', $this->template->path, $name);
    }

    /**
     * Gets path to the real view file
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Gets instance of template that view belongs to
     * @return Presto\Template
     */
    public function getTemplate()
    {
        return $this->template;
    }
}