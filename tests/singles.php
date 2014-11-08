<?php

use mrkmg\NativeSass;

require(dirname(__FILE__) . "/../src/mrkmg/NativeSass/Compiler.php");

$compiler = new \mrkmg\NativeSass\Compiler;

$compiler->config(array(
    'compilerPath'  => 'sass',
    'inputPath'     => dirname(__FILE__) . '/sass',
    'outputPath'    => dirname(__FILE__) . '/css',
    'outputStyle'   => 'expanded',
    'sourceMap'     => 'file',
));

$compiler->compileSingle('simplescss.scss');
$compiler->compileSingle('simplesass.sass');

