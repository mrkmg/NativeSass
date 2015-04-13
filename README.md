NativeSass
==========


NativeSass will compile your sass and scss files using the sass command on your system.
It allows for a variety of options and comes with a ServiceProvider for Laravel.


Installation
------------

First, ensure that Sass is installed on your system. To do that see [Sass Install](http://sass-lang.com/install).

Next, install NativeSass via composer.

    composer require mrkmg/native-sass

___With Laravel 4___

Add the service provider to your app config (app/config/app.php)

    'mrkmg\NativeSass\CompilerServiceProvider'
    
Install the configuration file
    
    php artisan config:publish mrkmg/native-sass

Update the configuration file to your needs.

Usage
----

___Generic Usage___

Instantiate a new instance of the compiler with the desired options.

    $compiler = new \mrkmg\NativeSass\Compiler([
        'compilerPath'  => 'sass',
        'inputPath'     => '/some/path/to/raw/sass',
        'outputPath'    => '/some/path/for/compiled/css',
        'outputStyle'   => 'expanded', //Options are nested, compact, compressed, or expanded
        'sourceMap'     => 'file', //Options are auto, inline, file, or none
    ]);
    
For all the following examples, if you pass a relative path, it is assumed to be relative to the `inputPath`. If
you pass an absolute path (any path starting with a /) then the path is used as is.
    
If you wish to compile a single file into CSS, use the `compileSingle` method.
    
    //Keep the same name. Will create rawfile.css and rawfile.css.map in the outputPath
    $compiler->compileSingle('rawfile.sass');
    
    //Custom output name and paths. Will create newname.css and newname.css.map in /some/other/output/
    $compiler->compileSingle('/some/other/path/rawfile.scss', '/some/other/output/newname.css');

You can also compile many files at once. Use the `compileMany` method which takes an array of files.
    
    //Compiles many files.
    $compiler->compileMany([
        'rawfile.sass',
        'rawfile2.sass',
        'rawfile3.sass' => 'alternatename.css'
    ]);

Finally, you can compile an entire directory of CSS files all at once with the `compileAll` method. By default, only
files in the top directory are compiled.
    
    //Compiles all files in the inputPath into the outputPath
    $compiler->compileAll();
    
    //Compiles all files in a specific directory into the outputPath
    $compiler->compileAll('/some/other/sourcedir/');
    
    //Compile all files in the inputPath, and 2 levels deep of sub-directories, into the outputPath
    $compiler->compileAll("", 2);


___With Laravel___


The class in instantiated automatically and all methods made available via the alias NativeSass.

For example, to run compile a single file:
    
    NativeSass::compileSingle('rawfile.sass');
    
Usage is the same as above.


Optional Command for Laravel 4
------------------------------

If you wish to compile all your sass via a console command in artisan, create the following command:

Create app/command/CompileSass.php

    <?php
    use Illuminate\Console\Command;
    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Input\InputArgument;
    
    class CompileSass extends Command {
    
    	/**
    	 * The console command name.
    	 *
    	 * @var string
    	 */
    	protected $name = 'sass:compile';
    
    	/**
    	 * The console command description.
    	 *
    	 * @var string
    	 */
    	protected $description = 'Compile all SASS.';
    
    	/**
    	 *
         */
    	public function __construct()
    	{
    		parent::__construct();
    	}
    
    	/**
    	 * Execute the console command.
    	 *
    	 * @return mixed
    	 */
    	public function fire()
    	{
    	    //Adjust here to your needs
    		NativeSass::compileAll();
    	}
    
    	/**
    	 * Get the console command arguments.
    	 *
    	 * @return array
    	 */
    	protected function getArguments()
    	{
    		return array(
    		);
    	}
    
    	/**
    	 * Get the console command options.
    	 *
    	 * @return array
    	 */
    	protected function getOptions()
    	{
    		return array(
    		);
    	}
    
    }

Then add that command to artisan. In app/start/artisan.php, add
    
    Artisan::add(new CompileSass);

Then compile all your sass
    
    php artisan sass:compile


License
-------

The MIT License (MIT)

Copyright (c) 2015 Kevin Gravier - [MrKMG](https://github.com/mrkmg)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.