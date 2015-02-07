<?php

namespace tatarko\presto;

/**
 * Presto Template engine
 * @author Tomas Tatarko <tomas@tatarko.sk>
 * @link https://github.com/tatarko/OpinerCMS
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @copyright Copyright &copy; 2012-2013 Tomas Tatarko
 * @since 1.7
 * @property-read string $title Nazov motivu
 * @property-read float $version Verzia motivu
 * @property-read float $system S akou verziou motivu je template kompatibilny
 * @property-read PrestoEngine\Author[] $authors Autori motivu
 * @property-read PrestoEngine\Layout[] $layouts Mozne layouty motivu
 * @property-read string $path Adresa priecinku s motivom
 * @property-read string $name Nazov priecinka motivu
 * @property-read string $cacheFolder Adresa cache priecinku
 * @property PrestoEngine\Layout $layout Aktivny layout
 */
class Template extends Object
{
    /**
     * Maska pre sprintf, na zaklade ktorej sa ziskavaju adresy priecinkov motivom (__FILE__, $templateFolderName)
     */
    const TEMPLATE_FOLDER_MASK = '%s/../../templates/%s/';

    /**
     * Staticke premenne na renderovanie
     * @var array
     */
    public $values = array();

    /**
     * Adresa priecinku s motivom
     * @var string
     */
    protected $path;

    /**
     * Nazov priecinka motivu
     * @var string
     */
    protected $name;

    /**
     * Aktivny layout
     * @var PrestoEngine\Layout
     */
    protected $layout;

    /**
     * Udaje nacitane z manifest.json suboru
     * @var PrestoEngine\Author[]
     */
    protected $manifestData = array();

    /**
     * Mozne layouty motivu
     * @var PrestoEngine\Layout[]
     */
    protected $layouts = array();

    /**
     * Konstruktor templatu
     * @param string $name Nazov priecinku daneho templatu
     * @throws Exception
     */
    public function __construct($name)
    {

        $templatePath = $this->resolveTemplatePath($name);

        if (!is_dir($templatePath)) {

            throw new Exception(sprintf('Template "%s" was not found in the filesystem', $name), 404);
        }

        $this->path = $templatePath;
        $this->name = $name;
        $this->readManifest();
    }

    /**
     * Precita manifest daneho templatu
     * @throws Exception Ak manifest subor neexistuje
     * @suports Method-Chaining
     */
    protected function readManifest()
    {

        $manifestFile = $this->path . 'manifest.json';

        if (!file_exists($manifestFile)) {

            throw new Exception(sprintf('Manifest file does not exists in template %s', $this->name), 404);
        }

        $this->manifestData = json_decode(file_get_contents($manifestFile), true);
        $this->_s[] = 'manifestData';

        return $this->parseAuthors()->parseLayouts();
    }

    /**
     * Parsuj autorov motivu
     * @return \PrestoEngine\Template
     * @suports Method-Chaining
     */
    protected function parseAuthors()
    {

        if (!isset($this->manifestData['authors']) || !is_array($this->manifestData['authors'])) {

            $this->manifestData['authors'] = array();
        }

        foreach ($this->manifestData['authors'] as $key => $values) {

            $this->manifestData['authors'][$key] = new Author;
            $this->manifestData['authors'][$key]->attributes = $values;
        }

        return $this;
    }

    /**
     * Parsuj layouty motivu
     * @return \PrestoEngine\Template
     * @suports Method-Chaining
     */
    protected function parseLayouts()
    {

        if (!empty($this->layouts)) {

            return $this;
        }

        if (!isset($this->manifestData['layouts']) || !is_array($this->manifestData['layouts'])) {

            $this->manifestData['layouts'] = array();
        }

        foreach ($this->manifestData['layouts'] as $values) {

            $layout = new Layout($this);
            $layout->attributes = $values;
            $this->layouts[$layout->name] = $layout;
        }

        return $this;
    }

    /**
     * Skontroluje kompatibilitu motivu
     * @return \PrestoEngine\Template
     * @suports Method-Chaining
     */
    protected function checkCompatibility()
    {

        list(, $systemVersion) = explode('~$~', \SystemInfo);
        $blankSystemVersion = current(explode(' ', $systemVersion));

        if (version_compare($blankSystemVersion, $this->system, '<')) {

            throw new Exception('Template is not compatible with system version', 500);
        }

        return $this;
    }

    /**
     * Ziska zoznam layoutov
     * @return PrestoEngine\Layout[]
     */
    public function getLayouts()
    {

        return $this->layouts;
    }

    /**
     * Vrati aktivny layout
     * @return PrestoEngine\Layout
     */
    public function getLayout()
    {

        return $this->layout ? : current($this->layouts);
    }

    /**
     * Vrati cestu k priecinku daneho motivu
     * @return string
     */
    public function getPath()
    {

        return $this->path;
    }

    /**
     * Setnutie aktivneho layoutu
     * @param string|\PrestoEngine\Layout $layout Nazov alebo instancie layoutu
     * @throws Exception Ak sa layout nenajde
     */
    public function setLayout($layout)
    {

        if ($layout instanceof Layout) {

            return $this->layout = $layout;
        }

        foreach ($this->layouts as $name => $model) {

            if ($name == $layout) {

                return $this->layout = $model;
            }
        }

        throw new Exception('Requested layout does not exists', 404);
    }

    /**
     * Vrati adresu ku cache priecinku
     * @return string
     */
    public function getCacheFolder()
    {

        return dirname(__FILE__) . '/../../store/cache/';
    }

    /**
     * Ziska adresu priecinka motivu
     * @param string $name
     * @return string
     */
    protected function resolveTemplatePath($name)
    {

        return sprintf(self::TEMPLATE_FOLDER_MASK, dirname(__FILE__), $name);
    }

    /**
     * Renderovanie iba samotneho viewu
     * @param string $view Nazov viewu
     * @param array $values Premenne pre renderovanie
     * @param boolean $return Vratit vyrenderovany view?
     * @return string
     */
    public function renderPartial($view, array $values, $return = false)
    {

        $engine = new Engine(new View($this, $view, $values));
        return $engine->render($return);
    }

    /**
     * Renderovanie iba samotneho viewu
     * @param string $view Nazov viewu
     * @param array $values Premenne pre renderovanie
     * @param boolean $return Vratit vyrenderovany view?
     * @return string
     */
    public function render($view, array $values, $return = false)
    {

        $engine = new Engine(
            new LayoutView(
            $this, $this->getLayout()->name, array(
                'content' => $this->renderPartial($view, $values, true)) + $this->getLayout()->menuRenderValues
            )
        );
        $engine->escapeHtml = false;
        return $engine->render($return);
    }
}