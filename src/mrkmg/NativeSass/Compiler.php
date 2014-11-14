<?php

namespace mrkmg\NativeSass;

class Compiler
{
    const VERSION_CHECK_REGEX = '/Sass ([0-9.]+) \(.+\)/';
    const EXTENSIONS = '/(scss|sass)$/';

    /**
     * @var string Path to SASS Compiler
     */
    protected $compilerPath = "";

    /**
     * @var string Path to SASS/SCSS files
     */
    protected $inputPath = "";

    /**
     * @var string Path to save CSS output
     */
    protected $outputPath = "";

    /**
     * @var string SASS output style
     */
    protected $outputStyle = "nested";

    /**
     * @var string Source map type
     */
    protected $sourceMap = "auto";

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

        if (isset($options['outputStyle']))
            $this->setOutputStyle($options['outputStyle']);

        if (isset($options['sourceMap']))
            $this->setSourceMap($options['sourceMap']);

    }

    /**
     * @return string Path to output files
     */
    public function getOutputPath()
    {
        return $this->outputPath;
    }

    /**
     * @param $path string Path to CSS files
     * @throws \Exception
     */
    public function setOutputPath($path)
    {
        // Remove trailing slash
        if (substr($path,-1) == '/')
        {
            $path = substr($path, 0, strlen($path) - 1);
        }

        $this->outputPath = $path;
    }

    /**
     * @return string Path to input files
     */
    public function getInputPath()
    {
        return $this->inputPath;
    }

    /**
     * @param $path string Path to SASS/SCSS files
     * @throws \Exception
     */
    public function setInputPath($path)
    {
        // Remove trailing slash
        if (substr($path,-1) == '/')
        {
            $path = substr($path, 0, strlen($path) - 1);
        }

        $this->inputPath = $path;
    }

    /**
     * @return string SASS output option for style
     */
    public function getOutputStyle()
    {
        return $this->outputStyle;
    }

    /**
     * @param $outputStyle SASS output option of style
     * @throws \Exception
     */
    public function setOutputStyle($outputStyle)
    {
        if ( ! in_array($outputStyle, array(
            'nested',
            'compact',
            'compressed',
            'expanded'
        )))
        {
            throw new \Exception('Unknown output style.');
        }
        $this->outputStyle = $outputStyle;
    }

    /**
     * @return string SASS output option of style
     */
    public function getSourceMap()
    {
        return $this->sourceMap;
    }

    /**
     * @param $sourceMap
     * @throws \Exception
     */
    public function setSourceMap($sourceMap)
    {
        if ( ! in_array($sourceMap, array(
            'auto',
            'inline',
            'file',
            'none'
        )))
        {
            throw new \Exception('Unknown source map type.');
        }
        $this->sourceMap = $sourceMap;
    }

    /**
     * @param $path string Path to sass compiler
     * @throws \Exception
     */
    public function setCompilerPath($path)
    {
        $version_check_result = shell_exec("$path -v");

        if ( ! preg_match(self::VERSION_CHECK_REGEX, $version_check_result))
        {
            throw new \Exception($path . ' is not a valid path to the sass compiler.');
        }

        $this->compilerPath = $path;
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

        if ( ! is_dir(dirname($output_file)))
        {
            mkdir(dirname($output_file), 0777, true);
        }

        return $this->runCompileSingle($input_file, $output_file) == 0;
    }

    /**
     * @param array $file_list
     * @return bool
     * @throws \Exception
     */
    public function compileMany(array $file_list)
    {
        $result = true;

        foreach ($file_list as $index => $value)
        {
            if (is_int($index))
            {
                $result &= $this->compileSingle($value);
            }
            else
            {
                $result &= $this->compileSingle($index, $value);
            }
        }

        return $result;
    }

    /**
     * @param string $path Path to files
     * @param int $recursive_levels How many levels to search
     * @return bool Result of compilations
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

        $result = true;

        foreach($files as $file)
        {
            $output = str_replace($path, $this->getOutputPath(), preg_replace(self::EXTENSIONS, 'css', $file));

            $result &= $this->compileSingle($file, $output);
        }

        return $result;
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

            if( is_dir($file_path) && $recursive_levels > 0)
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
     * @param $input string file to compile
     * @param $output string output of compilation
     * @return int 0 is success, 1 or higher is failure
     */
    protected function runCompileSingle($input, $output)
    {
        exec(
                $this->compilerPath .
                ' -t ' . $this->getOutputStyle() .
                ' --sourcemap=' . $this->getSourceMap() .
                ' ' . $input . ' ' . $output,
            $result,
            $return
        );

        return $return;
    }

}