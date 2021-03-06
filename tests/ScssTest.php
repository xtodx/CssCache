<?php

    class ScssTest extends PHPUnit\Framework\TestCase
    {
        protected static $TestClass;

        public function setUp()
        {
            self::$TestClass = new \xtodx\csscache\CssCache(PATH, FOLDER);
        }


        /**
         * @dataProvider providerPath
         */
        public function testFirst($files)
        {
            ob_start();
            foreach ($files as $f) {
                if ($f[0] == "css") {
                    self::$TestClass->includeCss($f[1]);
                } elseif ($f[0] == "scss") {
                    self::$TestClass->includeScss($f[1]);
                }
            }
            $return = self::$TestClass->writeCss("full.css");
            ob_end_clean();
            if ($return) {
                $this->assertTrue(true, $return);
            }else{
                $this->assertFalse(false, $return);
            }
        }

        public function providerPath()
        {
            return [
                "okay" => [["css", "css"], ["scss", "scss"]],
                "okay2" => [["scss", "css"], ["css", "scss"]],
                "bad" => [["scss", "badscss"], ["scss", "scss"]],
            ];
        }

        public function tearDown()/* The :void return type declaration that should be here would cause a BC issue */
        {
            self::$TestClass = null;
            parent::tearDown(); // TODO: Change the autogenerated stub
        }
    }