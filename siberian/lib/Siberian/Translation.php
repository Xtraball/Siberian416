<?php

namespace Siberian;

/**
 * Class Translation
 * @package Siberian
 */
class Translation
{
    /**
     * Compare existing translations with all __() / ->_() calls and extract missing!
     */
    public static function extractAll ()
    {
        $allKeys = [];

        $languages = \Core_Model_Directory::getBasePathTo('/languages/default');
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($languages, 4096),
            \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                continue;
            }

            $extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
            if ($extension === 'csv') {
                if (($handle = fopen($file->getPathname(), 'r')) !== false) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                        $num = count($data);
                        for ($c=0; $c < $num; $c++) {
                            if (!in_array($data[$c], $allKeys)) {
                                $allKeys[] = $data[$c];
                            }
                        }
                    }
                    fclose($handle);
                }
            }
        }

        $languagesModules = \Core_Model_Directory::getBasePathTo('/app');
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($languagesModules, 4096),
            \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                continue;
            }

            $extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
            if ($extension === 'csv' &&
                strpos($file->getPathname(), '/default/') !== false) {
                if (($handle = fopen($file->getPathname(), 'r')) !== false) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                        $num = count($data);
                        for ($c=0; $c < $num; $c++) {
                            if (!in_array($data[$c], $allKeys)) {
                                $allKeys[] = $data[$c];
                            }
                        }
                    }
                    fclose($handle);
                }
            }
        }

        $extractTranslate = [];

        $appPath = \Core_Model_Directory::getBasePathTo('/app');
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($appPath, 4096),
            \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                continue;
            }

            $extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
            if (in_array($extension, ['php', 'phtml', 'csv'])) {
                $textContent = file_get_contents($file->getPathname());
                $count = preg_match_all('/((__|->_)\("([!\w\s\d\'<>\/\\\,:;.]+)(",|"\)))/mi', $textContent, $matches);
                if ($count > 0) {
                    foreach ($matches[3] as $element) {
                        if (!in_array($element, $extractTranslate)) {
                            $extractTranslate[] = $element;
                        }
                    }
                }
                $count = preg_match_all('/((__|->_)\(\'([!\w\s\d"<>\/\\\,:;.]+)(\',|\'\)))/mi', $textContent, $matches);
                if ($count > 0) {
                    foreach ($matches[3] as $element) {
                        if (!in_array($element, $extractTranslate)) {
                            $extractTranslate[] = $element;
                        }
                    }
                }


            }
        }

        foreach ($extractTranslate as $extract) {
            if (!in_array($extract, $allKeys)) {
                echo '"' . $extract . '"' . PHP_EOL;
            }
        }
    }
}