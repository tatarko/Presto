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

namespace Presto;

/**
 * Simple wrapper class for authors of template
 * @package Presto
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @copyright (c) 2013, Tomas Tatarko
 * @link https://github.com/tatarko/Presto
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @since 1.0
 * @property-read string $htmlLink Html link to author's homepage
 */
class Author extends Object
{
    /**
     * Name of the author
     * @var string
     */
    public $name;

    /**
     * Homepage of the author
     * @var string
     */
    public $url;

    /**
     * Gets html link to author's homepage
     * @return string
     */
    public function getHtmlLink()
    {
        return sprintf('<a href="%s">%s</a>', $this->url, $this->name);
    }
}