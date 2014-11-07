<?php

namespace mrkmg\NativeSass;

class Compiler
{
    const VERSION_CHECK_REGEX = '/Sass ([0-9.]+) \(.+\)/';

    const EXTENSIONS = '/(scss|sass)$/';

    /**
     * @var string Path to Sass Compiler
     */
    protected $compilerPath = "";

    /**
     * @var string Path to Sass/Scss files
     */
    protected $inputPath = "";

    /**
     * @var string Path to save CSS output
     */
    protected $outputPath = "";

    //Public

    /**
     * Class Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        if($options)
        {
            $this->config($options);
        }
    }

    /**
     * Setup up configuration
     *
     * @param array $options
     * @throws \Exception
     */
    public function config(array $options)
    {
        if (isset($options['compilerPath']))
            $this->setCompilerPath($options['compilerPath']);

        if (isset($options['inputPath']))
            $this->setInputPath($options['inputPath']);

        if (isset($options['outputPath']))
            $this->setOutputPath($options['outputPath']);

    }

    /**
     * @param string $input_file
     * @param string $output_file
     * @return bool
     * @throws \Exception
     */
    public function compileSingle($input_file, $output_file = "")
    {
        if (substr($input_file,0,1) != '/')
        {
            $input_file = $this->getInputPath() . '/' . $input_file;
        }

        if ( ! file_exists($input_file))
        {
            throw new \Exception($input_file . ' could not be found.');
        }

        if (empty($output_file))
        {
            $output_file = $this->outputPath . '/' . preg_replace(self::EXTENSIONS, 'css', basename($input_file));
        }
        else
        {
            if (substr($output_file,0,1) != '/')
            {
                $output_file = $this->outputPath . '/' . $output_file;
            }
        }

        return $this->runCompileSingle($input_file, $output_file) == 0;
    }

    /**
     * @param string $path Path to files
     * @param int $recursive_levels How many levels to search
     * @throws \Exception
     */
    public function compileAll($path = "", $recursive_levels = 0)
    {
        if (empty($path))
        {
            $path = $this->getInputPath();
        }

        if ( ! is_dir($path))
        {
            throw new \Exception($path . ' is not a directory.');
        }

        $files = $this->getMatchingFilesInDirectory($path, $recursive_levels);

        foreach($files as $file)
        {
            $this->compileSingle($file);
        }
    }





    //Protected

    /**
     * @param $path string Path to files to search for
     * @param $recursive_levels how many levels to search for files
     * @return array list of files matching
     */
    protected function getMatchingFilesInDirectory($path, $recursive_levels)
    {
        $files = array();

        $dir = opendir($path);

        while(false !== ($file = readdir($dir)))
        {
            if ($file == '.' || $file == '..') continue;

            $file_path = $path . '/' . $file;

            if( is_dir($file_path) && $recursive_levels >= 0)
            {
                $files = array_merge($files, $this->getMatchingFilesInDirectory($file_path, $recursive_levels - 1));
            }
            elseif( is_file($file_path) && preg_match(self::EXTENSIONS, $file_path))
            {
                $files[] = $file_path;
            }
        }

        closedir($dir);

        return $files;
    }


    /**
     * @param $path string Path to sass compiler
     * @throws \Exception
     */
    protected function setCompilerPath($path)
    {
        $version_check_result = shell_exec("$path -v");

        if ( ! preg_match(self::VERSION_CHECK_REGEX, $version_check_result))
        {
            throw new \Exception($path . ' is not a valid path to the sass compiler.');
        }

        $this->compilerPath = $path;
    }

    /**
     * @param $path string Path to CSS files
     * @throws \Exception
     */
    protected function setOutputPath($path)
    {
        // Remove trailing slash
        if (substr($path,-1) == '/')
        {
            $path = substr($path, 0, strlen($path) - 1);
        }

        $this->outputPath = $path;
    }

    /**
     * @param $path string Path to SASS/SCSS files
     * @throws \Exception
     */
    protected function setInputPath($path)
    {
        // Remove trailing slash
        if (substr($path,-1) == '/')
        {
            $path = substr($path, 0, strlen($path) - 1);
        }

        $this->inputPath = $path;
    }

    /**
     * @return string Path to input files
     */
    protected function getInputPath()
    {
        return $this->inputPath;
    }

    /**
     * @return string Path to output files
     */
    protected function getOutputPath()
    {
        return $this->outputPath;
    }

    /**
     * @param $input string file to compile
     * @param $output string output of compilation
     * @return int
     */
    protected function runCompileSingle($input, $output)
    {
        exec($this->compilerPath . ' ' . $input . ' ' . $output, $result, $return);

        return $return;
    }

}