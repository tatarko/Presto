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
 * Wrapper for layout
 * @package presto
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @copyright (c) 2013, Tomas Tatarko
 * @link https://github.com/tatarko/Presto
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @since 1.0
 */
class Layout extends Object
{
    /**
     * Code-Name of the layout
     * @var string
     */
    public $name;

    /**
     * Human-Name of the layout
     * @var string
     */
    public $title;

    /**
     * List of navigations (menus) that list layout offers
     * @var Menu[]
     */
    protected $menus;

    /**
     * Pointer to the template that this layout belongs to
     * @var Template
     */
    protected $template;

    /**
     * Constructing new instance of layout
     * @param Template $template Instance of the template that layout belongs to
     */
    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    /**
     * Sets layout's menu list
     * @param array $menus
     */
    public function setMenus(array $menus)
    {
        foreach ($menus as $values) {
            $menu = new Menu($this);
            $menu->attributes = $values;
            $this->menus[] = $menu;
        }
    }

    /**
     * Gets menu list
     * @return Menu[]
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * Gets rendering values for menus
     * @return string[]
     */
    public function getMenuRenderValues()
    {
        $menus = array();

        foreach ($this->menus as $key => $menu) {
            $key = sprintf('menu%d', $key + 1);

            if (!isset($this->template->values[$key])) {
                continue;
            }

            $engine = new Engine(new MenuView($this->template, $menu->view, $this->template->values[$key]));
            $menus[$key] = $engine->render(true);
        }

        return $menus;
    }
}
