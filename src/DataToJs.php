<?php

namespace Dennykuo;

class DataToJs
{
    /**
     * The namespace to nest JS variables under.
     *
     * @var string
     */
    protected $namespace;

     /**
     * The JS indent.
     *
     * @var string
     */
    protected $indent;

    /**
     * Output the JS variables.
     *
     * @var string
     */
    protected $output;

    /**
     * Create a new JS transformer instance.
     *
     * @param string $namespace
     * @param int $indent
     */
    public function __construct($namespace = 'window', $indent = 0)
    {
        $this->namespace = $namespace;
        $this->indent = $indent;
    }

    /**
     * Bind the given array of variables to the view.
     * return $this
     */
    public function put()
    {
        $js = $this->constructJavaScript($this->normalizeInput(func_get_args()));

        $this->output = $js;

        return $this;
    }

    /**
     * Outout variables to the page.
     * @return void
     */
    public function output()
    {
        echo $this->output;
    }

    /**
     * Translate the array of PHP variables to a JavaScript syntax.
     *
     * @param  array $variables
     * @return array
     */
    public function constructJavaScript($variables)
    {
        $output = [];
        $output[] = $this->constructNamespace() .  " \n";

        foreach ($variables as $name => $value) {
            $indent = str_repeat("\t", $this->indent);
            $output[] = $indent . $this->initializeVariable($name, $value) . " \n";
        }

        return implode('', $output);
    }

    /**
     * Create the namespace to which all vars are nested.
     *
     * @return string
     */
    protected function constructNamespace()
    {
        if ($this->namespace == 'window') {
            return '';
        }

        return "window.{$this->namespace} = window.{$this->namespace} || {};";
    }

    /**
     * Translate a single PHP var to JS.
     *
     * @param  string $key
     * @param  string $value
     * @return string
     */
    protected function initializeVariable($key, $value)
    {
        return "{$this->namespace}.{$key} = {$this->convertToJavaScript($value)};";
    }

    /**
     * Format a value for JavaScript.
     *
     * @param  string $value
     * @throws Exception
     * @return string
     */
    protected function convertToJavaScript($value)
    {
        if (! is_object($value))
            return json_encode($value);

        return $this->transformObjectData($value);
    }

    /**
     * Normalize the input arguments.
     *
     * @param  mixed $arguments
     * @return array
     * @throws \Exception
     */
    protected function normalizeInput($arguments)
    {
        if (is_array($arguments[0])) {
            return $arguments[0];
        }
        
        if (count($arguments) == 2) {
            return [$arguments[0] => $arguments[1]];
        }

        throw new \Exception('Try JavaScript::put(["foo" => "bar"])');
    }

    /**
     *
     * @param  mixed $value
     * @throws Exception
     * @return string
     */
    protected function transformObjectData($value)
    {
        if ($value instanceof \JsonSerializable || $value instanceof \StdClass) {
            return json_encode($value);
        }

        // Otherwise, if the object doesn't even have a __toString() method, we can't proceed.
        if (! method_exists($value, '__toString')) {
            throw new \Exception('Cannot transform this object to JavaScript.');
        }

        return "'{$value}'";
    }
}
