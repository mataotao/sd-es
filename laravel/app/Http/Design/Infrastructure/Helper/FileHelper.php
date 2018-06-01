<?php
namespace App\Http\Design\Infrastructure\Helper;


class FIleHelper
{
    
    /**写入文件
     * @param $fileName
     * @param $data
     * @param $path
     * @return array|int
     */
    public static function FilePutContent($fileName, $data, $path = '')
    {
        if (empty($fileName) || empty($data)) {
            return [];
        }
        $fileName    = ucfirst($fileName);
        $fileNameOne = $path . "one$fileName";
        $fileNameTwo = $path . "two$fileName";
        if (file_exists($fileNameOne) && file_exists($fileNameTwo)) {
            $fileOneTime     = filemtime($fileNameOne);
            $fileTwoTime     = filemtime($fileNameTwo);
            $finallyFileName = $fileOneTime > $fileTwoTime ? $fileNameTwo : $fileNameOne;
        } elseif (file_exists($fileNameOne) && !file_exists($fileNameTwo)) {
            $finallyFileName = $fileNameTwo;
        } elseif (!file_exists($fileNameOne) && file_exists($fileNameTwo)) {
            $finallyFileName = $fileNameOne;
        } elseif (!file_exists($fileNameOne) && !file_exists($fileNameTwo)) {
            $finallyFileName = $fileNameOne;
        }
        $res = file_put_contents($finallyFileName, $data);
        
        return $res;
    }
    
    /**
     * 读取文件
     * @param $fileName
     * @param $path
     * @return array|string
     */
    public static function FileGetContent($fileName, $path = '')
    {
        if (empty($fileName)) {
            return [];
        }
        $fileName    = ucfirst($fileName);
        $fileNameOne = $path . "one$fileName";
        $fileNameTwo = $path . "two$fileName";
        if (!file_exists($fileNameOne) && !file_exists($fileNameTwo)) {
            $finallyFileName = '';
        } elseif (file_exists($fileNameOne) && file_exists($fileNameTwo)) {
            $fileTimeOne     = filemtime($fileNameOne);
            $fileTimeTwo     = filemtime($fileNameTwo);
            $finallyFileName = $fileTimeOne > $fileTimeTwo ? $fileNameOne : $fileNameTwo;
        } elseif (file_exists($fileNameOne) && !file_exists($fileNameTwo)) {
            $finallyFileName = $fileNameOne;
        } elseif (!file_exists($fileNameOne) && file_exists($fileNameTwo)) {
            $finallyFileName = $fileNameTwo;
        }
        if (empty($finallyFileName)) {
            return [];
        } else {
            return file_get_contents($finallyFileName);
        }
    }
}
