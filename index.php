<?php

$config = parse_ini_file("config.ini", true);
$path = $config['config']['path'];


include_once('./library/ScanningFile.php');
include_once('./library/CountingFile.php');


printData($path);

function printData($directory)
{
    $id = processData($directory);
    $showFile = new CountingFile();
    if (!empty($id)) {
        $showFile->flag = CountingFile::FIRST_COUNT;
        $showFile->idFile = $id;
    }
    $showFile->printData();
}

function processData($directory)
{
    $idFiles = [];
    $scannedFile = new ScanningFile();
    $scannedFile->pathDir = $directory;
    $result = $scannedFile->scanDirectory();
    if (empty($result)) {
        echo 'Can`t find any file';
    }

    foreach ($result as $key => $file) {
        $filterFile = new CountingFile();
        $filterFile->pathFile = $file;
        $filterFile->totalFiles = count($result);
        $id = $filterFile->mapingData();
        if ($id !== null) {
            array_push($idFiles, $id);
        }
    }
    return $idFiles;
}

?>