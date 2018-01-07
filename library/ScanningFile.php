<?php
/**
 * Scan Directories
 * @author Riza Fauzi Rahman <riza.fauzi.rahman@gmail.com>
 * @since 2018.01.07
 */

class ScanningFile
{
    public $pathDir;

    public function scanDirectory()
    {
        $files = [];
        $directories = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->pathDir)
        );
        foreach ($directories as $file) {
            if (!$file->isDir() && preg_match("/^[^\.].*$/", $file->getFilename())){
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }
}
