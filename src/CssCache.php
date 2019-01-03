<?php

    namespace xtodx\csscache;

    use PHPUnit\Runner\Exception;

    class CssCache
    {
        private $folder, $path, $files = array();

        function __construct($folder, $path)
        {
            $this->folder = $folder;
            $this->path = $path;
        }

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
            try {
                if (file_exists($this->path . $this->folder . $name)) {
                    $ft = filemtime($this->path . $this->folder . $name);
                } else {
                    $ft = 0;
                }
                $tr = false;
                foreach ($this->files as $file) {
                    if (file_exists($this->path . $this->folder . $file[1])) {
                        $time = filemtime($this->path . $this->folder . $file[1]);
                        if ($time <> $ft || $tr) {
                            $tr = true;
                        }
                    }
                }
                if ($tr) {
                    $fp = fopen($this->path . $this->folder . $name, "w");
                    $time = time();
                    $text = "/*$time*/";
                    foreach ($this->files as $file) {
                        if ($file[0] == "css") {
                            $text .= file_get_contents($this->path . $this->folder . $file[1], true);
                            touch($this->path . $this->folder . $file[1], $time);
                        } elseif ($file[0] == "sass") {
                            $Compiler = new Scss();
                            $sass = file_get_contents($this->path . $this->folder . $file[1], true);
                            //Use Indented syntax : SASS.
                            $compiled = $Compiler->compile($sass);   //Try to parse.
                            $text .= $compiled;
                            touch($this->path . $this->folder . $file[1], $time);

                        }
                    }
                    $text = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $text);
                    $text = str_replace(array("\r\n", "\r", "\n", "\t"), '', $text);
                    $text = str_replace(array('  ', '    ', '    '), ' ', $text);
                    fwrite($fp, $text);
                    fclose($fp);
                    touch($this->path . $this->folder . $name, $time);
                }
                return $name;
            } catch (Exception $exception) {
                echo $exception->getMessage();
                return false;
            }
        }

    }