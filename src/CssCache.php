<?php

    namespace system\modules;

    use system\App;

    class CssCache
    {
        private $folder, $url, $files = array();

        function __construct($folder, $url)
        {
            $this->folder = $folder;
            $this->url = $url;
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
            if (file_exists($this->url . $this->folder . $name)) {
                $ft = filemtime($this->url . $this->folder . $name);
            } else {
                $ft = 0;
            }
            $tr = false;
            foreach ($this->files as $file) {
                if (file_exists($this->url . $this->folder . $file[1])) {
                    $time = filemtime($this->url . $this->folder . $file[1]);
                    if ($time <> $ft || $tr) {
                        $tr = true;
                    }
                }
            }
            if ($tr) {
                $fp = fopen($this->url . $this->folder . $name, "w");
                $time = time();
                $text = "/*$time*/";
                foreach ($this->files as $file) {
                    if ($file[0] == "css") {
                        $text .= file_get_contents($this->url . $this->folder . $file[1], true);
                        touch($this->url . $this->folder . $file[1], $time);
                    } elseif ($file[0] == "sass") {
                        $Compiler = new Scss();
                        $sass = file_get_contents($this->url . $this->folder . $file[1], true);
                        //Use Indented syntax : SASS.
                        $compiled = $Compiler->compile($sass);   //Try to parse.
                        $text .= $compiled;
                        touch($this->url . $this->folder . $file[1], $time);

                    }
                }
                $text = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $text);
                $text = str_replace(array("\r\n", "\r", "\n", "\t"), '', $text);
                $text = str_replace(array('  ', '    ', '    '), ' ', $text);
                fwrite($fp, $text);
                fclose($fp);
                touch($this->url . $this->folder . $name, $time);
            }
            echo "<link rel=\"stylesheet\" href=\"{$name}\">";
        }

    }