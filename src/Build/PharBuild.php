<?php
/**
 * Build phar archive (cli-task)
 *
 * @package go\Request
 * @author Grigoriev Oleg aka vasa_c
 */

namespace go\Request\Build;

class PharBuild extends \go\Request\CLI\Task
{
    /**
     * @override \go\Request\CLI\Task
     */
    protected function process()
    {
        $version = $this->stack->getNextArgument();
        if (!$version) {
            $this->error('Please specify a version of the library');
            $this->error('Example: ./build.php 1.3-rc2');
            return false;
        }
        $filename = $this->options->filename;
        if (!$filename) {
            $filename = 'Request-'.$version.'.phar';
        }
        if (\file_exists($filename)) {
            $this->error('File "'.$filename.'" already exists');
            return false;
        }
        $compression = $this->options->compression;
        if (!isset($this->compressFormats[$compression])) {
            $this->error('Invalid compress format "'.$compression.'"');
            return false;
        }
        return $this->createPhar($filename, $this->compressFormats[$compression]);
    }

    /**
     * @param string $filename
     * @param int $compression
     * @return boolean
     */
    private function createPhar($filename, $compression)
    {
        try {
            $phar = new \Phar($filename);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return false;
        }
        if (!$phar->canWrite()) {
            $this->error('Phar is read-only. See phar.readonly in php.ini');
            return false;
        }
        if ($compression !== \Phar::NONE) {
            if (!$phar->canCompress($compression)) {
                $this->error('Compression is not available');
                return false;
            }
        }
        try {
            @$phar->buildFromDirectory(__DIR__.'/../');
            if ($compression !== \Phar::NONE) {
                $phar->compressFiles($compression);
            }
            $phar->setDefaultStub('phar.php');
        } catch (\PharException $e) {
            $this->error($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @var array
     */
    protected $format = array(
        'title' => 'Build phar archive',
        'version' => '1.0',
        'copyright' => 'Grigoriev Oleg aka vasa_c, 2013',
        'usage' => './build.php [options] lib-version',
        'options' => array(
            'filename' => array(
                'title' => 'filename of phar archive (by default phar/Request-{version})',
                'short' => 'f',
                'filter' => 'Value',
            ),
            'compression' => array(
                'title' => 'compress (gz, bz, none), by default "gz"',
                'short' => 'c',
                'default' => 'gz',
                'filter' => 'Value',
            ),
        ),
        'short_parsing' => 'value',
    );

    /**
     * @var array
     */
    private $compressFormats = array(
        'gz' => \Phar::GZ,
        'bz' => \Phar::BZ2,
        'none' => \Phar::NONE,
    );
}
