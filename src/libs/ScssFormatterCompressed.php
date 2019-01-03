<?php

    namespace xtodx\csscache\libs;


class ScssFormatterCompressed extends ScssFormatter {
    public $open = "{";
    public $tagSeparator = ",";
    public $assignSeparator = ":";
    public $break = "";

    public function indentStr($n = 0) {
        return "";
    }
}