<?php

use mrkmg\NativeSass;

require(dirname(__FILE__) . "/../src/mrkmg/NativeSass/Compiler.php");

$compiler = new \mrkmg\NativeSass\Compiler;

$files = glob(dirname(__FILE__) . '/css/*'); // get all file names
foreach($files as $file){ // iterate files
    if(is_file($file))
        unlink($file); // delete file
}

$compiler->config(array(
    'compilerPath'  => 'sass',
    'inputPath'     => dirname(__FILE__) . '/sass',
    'outputPath'    => dirname(__FILE__) . '/css',
    'outputStyle'   => 'compact',
    'sourceMap'     => 'none',
));

$compiler->compileAll();

$desiredResults = array(
    dirname(__FILE__) . '/css/simplescss.css',
    dirname(__FILE__) . '/css/simplesass.css',
);

$all_created = true;

foreach ($desiredResults as $file)
{
    if ( ! file_exists($file))
    {
        $all_created = false;
        break;
    }
}

if ($all_created)
{
    echo "PASSED\n";
}
else
{
    echo "FAILED\n";
}