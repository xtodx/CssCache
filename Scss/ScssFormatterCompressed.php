<?php

namespace system\modules\Scss;


use system\modules\Scss\Scss;
class ScssFormatterCompressed extends ScssFormatter {
    public $open = "{";
    public $tagSeparator = ",";
    public $assignSeparator = ":";
    public $break = "";

    public function indentStr($n = 0) {
        return "";
    }
}