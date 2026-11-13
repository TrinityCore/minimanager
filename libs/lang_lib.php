<?php

class lang
{
    private $lang; // contains the used language
    private $modules; // contains the loaded language modules

    public function __construct($lang, $global)
    {
        $this->modules['global'] = $global;
    }

    public function loadModule($name, $module)
    {
        if(!isset($this->modules[$name]))
        {
            $this->modules[$name] = $module;
        }
    }

    public function _($module, $index)
    {
        if(isset($this->modules[$module][$index]))
            return $this->modules[$module][$index];
        else
            return '{' . $index . '}';
    }

    public function exists($module, $index)
    {
        return isset($this->modules[$module][$index]);
    }
}

?>
