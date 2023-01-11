<?php

require_once('autoload.php');

$rpn = (new ReversePolishNotation())
    ->run('1 / 2 * 5 - 1 + 1 * (2 * 3 * 4) / 11');

var_dump($rpn);
