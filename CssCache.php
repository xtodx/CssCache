<?php

namespace system\modules;

use system\App;

class CssCache
{
    var $files = array();

    public function includeCss($file)
    {
        $this->files[] = ["css", $file];
    }

    public function includeSass($file)
    {
        $this->files[] = ["sass", $file];
    }

    public function writeCss($name)
    {
        $folder = App::$app->config['FOLDER'];
        $url = App::$app->APP_PATH;
        if (file_exists($url . $folder . $name))
            $ft = filemtime($url . $folder . $name);
        else
            $ft = 0;
        $tr = false;
        foreach ($this->files as $file) {
            if (file_exists($url . $folder . $file[1])) {
                $time = filemtime($url . $folder . $file[1]);
                if ($time <> $ft || $tr) {
                    $tr = true;
                }
            }
        }
        if ($tr) {
            $fp = fopen($url . $folder . $name, "w");
            $time = time();
            $text = "/*$time*/";
            foreach ($this->files as $file) {
                if ($file[0] == "css") {
                    $text .= file_get_contents($url . $folder . $file[1], true);
                    touch($url . $folder . $file[1], $time);
                } elseif ($file[0] == "sass") {
                    $Compiler = new Scss();
                    $sass = file_get_contents($url . $folder . $file[1], true);
                    //Use Indented syntax : SASS.
                    $compiled = $Compiler->compile($sass);   //Try to parse.
                    $text .= $compiled;
                    touch($url . $folder . $file[1], $time);

                }
            }
            $text = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $text);
            $text = str_replace(array("\r\n", "\r", "\n", "\t"), '', $text);
            $text = str_replace(array('  ', '    ', '    '), ' ', $text);
            fwrite($fp, $text);
            fclose($fp);
            touch($url . $folder . $name, $time);
        }
        echo "<link rel=\"stylesheet\" href=\"{$name}\">";
    }

}