<?php

return array(
    // Default source directory
    'inputPath'     => public_path() . '/sass',

    //Default output directory
    'outputPath'    => public_path() . '/css',

    //Default compiler path
    'compilerPath'  => 'sass',

    //Style of output (NESTED, COMPACT, COMPRESSED, EXPANDED)
    'outputStyle'    => \mrkmg\NativeSass\CompilerOutputStyle::NESTED,

    //Sourcemap type (AUTO, INLINE, FILE, NONE)
    'sourceMap'     => \mrkmg\NativeSass\CompilerSourceMap::AUTO
);